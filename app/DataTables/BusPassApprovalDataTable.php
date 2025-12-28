<?php

namespace App\DataTables;

use App\Models\BusPassApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class BusPassApprovalDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('app_id', function ($row) {
                $html = '<strong>#' . $row->id . '</strong>';

                // Check if recently not recommended
                if (method_exists($row, 'wasRecentlyNotRecommended') && $row->wasRecentlyNotRecommended()) {
                    $html .= '<br><span class="badge badge-warning"><i class="fas fa-undo"></i> Returned from Branch</span>';
                }

                // Check if recently DMOV not recommended
                if (method_exists($row, 'wasRecentlyDmovNotRecommended') && $row->wasRecentlyDmovNotRecommended()) {
                    $html .= '<br><span class="badge badge-warning"><i class="fas fa-undo"></i> Returned from DMOV</span>';
                }

                return $html;
            })
            ->addColumn('person_details', function ($row) {
                $html = '<strong>' . e($row->person_name) . '</strong><br>';

                if ($row->regiment_no) {
                    $html .= '<small class="text-muted">Reg No: ' . e($row->regiment_no) . '</small>';
                } else {
                    $html .= '<small class="badge badge-success">Civil</small>';
                }

                return $html;
            })
            ->addColumn('service_details', function ($row) {
                $html = '';

                if ($row->rank) {
                    $html .= '<strong>Rank:</strong> ' . e($row->rank) . '<br>';
                }

                if ($row->unit) {
                    $html .= '<small class="text-muted">Unit: ' . e($row->unit) . '</small>';
                }

                return $html ?: '<span class="text-muted">N/A</span>';
            })
            ->addColumn('bus_pass_type', function ($row) {
                // Generate type label and badge color from bus_pass_type field
                $typeConfig = [
                    'daily_travel' => ['label' => 'Daily Travel (Living out)', 'badge' => 'badge-primary'],
                    'weekend_monthly_travel' => ['label' => 'Weekend and Living in Bus', 'badge' => 'badge-success'],
                    'living_in_only' => ['label' => 'Living in Bus only', 'badge' => 'badge-warning'],
                    'weekend_only' => ['label' => 'Weekend only', 'badge' => 'badge-info'],
                    'unmarried_daily_travel' => ['label' => 'Unmarried Daily Travel', 'badge' => 'badge-secondary']
                ];

                $config = $typeConfig[$row->bus_pass_type] ?? ['label' => ucfirst(str_replace('_', ' ', $row->bus_pass_type ?? 'N/A')), 'badge' => 'badge-light'];

                $html = '<span class="badge ' . $config['badge'] . '">' . e($config['label']) . '</span>';

                // Add SLTB season badge if available
                if ($row->obtain_sltb_season === 'yes') {
                    $html .= ' <span class="badge badge-warning ml-1" title="SLTB Season Available"><i class="fas fa-bus"></i> SLTB</span>';
                }

                return $html;
            })
            ->addColumn('branch_directorate', function ($row) {
                if ($row->establishment_name) {
                    return '<span class="badge badge-info">' . e($row->establishment_name) . '</span>';
                }
                return '<span class="text-muted">' . e($row->branch_directorate ?? 'Not specified') . '</span>';
            })
            ->addColumn('submitted', function ($row) {
                return $row->created_at ? $row->created_at->format('d M Y') : '';
            })
            ->addColumn('action', function ($row) {
                // Determine modal target based on person type
                $modalTarget = '#viewModal' . $row->id; // Default modal

                $html = '<div class="btn-group" role="group">';
                $html .= '<button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="' . $modalTarget . '" title="View"><i class="fas fa-eye"></i></button>';
                $html .= '</div>';

                return $html;
            })
            ->rawColumns(['app_id', 'person_details', 'service_details', 'bus_pass_type', 'branch_directorate', 'submitted', 'action'])
            ->setRowId('id')
            ->setRowClass(function ($row) {
                $hasNotRecommended = method_exists($row, 'wasRecentlyNotRecommended') && $row->wasRecentlyNotRecommended();
                $hasDmovNotRecommended = method_exists($row, 'wasRecentlyDmovNotRecommended') && $row->wasRecentlyDmovNotRecommended();

                return ($hasNotRecommended || $hasDmovNotRecommended) ? 'table-warning' : '';
            });
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(BusPassApplication $model): QueryBuilder
    {
        $user = Auth::user();
        $statuses = $this->getPendingStatusesForUserRole($user);

        if (empty($statuses)) {
            return $model->newQuery()->whereRaw('1 = 0'); // Return empty query
        }

        $query = $model->newQuery()
            ->select([
                'bus_pass_applications.*',
                'persons.name as person_name',
                'persons.regiment_no',
                'persons.rank',
                'persons.unit',
                'establishments.name as establishment_name'
            ])
            ->leftJoin('persons', 'bus_pass_applications.person_id', '=', 'persons.id')
            ->leftJoin('establishments', 'bus_pass_applications.establishment_id', '=', 'establishments.id')
            ->whereIn('bus_pass_applications.status', $statuses);

        // Filter by establishment for branch roles
        if ($user->isBranchUser()) {
            $query->where('bus_pass_applications.establishment_id', $user->establishment_id);
        }

        return $query->orderBy('bus_pass_applications.created_at', 'asc');
    }

    /**
     * Get the pending statuses that a user role should handle
     */
    private function getPendingStatusesForUserRole($user)
    {
        if ($user->hasRole('Bus Pass Subject Clerk (Branch)')) {
            return ['pending_subject_clerk'];
        }

        if ($user->hasRole('Staff Officer (Branch)')) {
            return ['pending_staff_officer_branch'];
        }

        if ($user->hasRole('Subject Clerk (DMOV)')) {
            return ['forwarded_to_movement'];
        }

        if ($user->hasRole('Staff Officer 2 (DMOV)')) {
            return ['pending_staff_officer_2_mov'];
        }

        if ($user->hasRole('Col Mov (DMOV)')) {
            return ['pending_col_mov'];
        }

        if ($user->hasRole('Director (DMOV)')) {
            return ['pending_col_mov'];
        }

        return [];
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('approvals-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->serverSide(true)
            ->processing(true)
            ->pageLength(25)
            ->lengthMenu([10, 25, 50, 100])
            ->orderBy(5, 'asc') // Order by submitted date
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('print'),
                Button::make('reload')
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('app_id')->title('App ID')->searchable(false)->orderable(false),
            Column::make('person_details')->title('Person Details')->name('persons.name')->searchable(true)->orderable(false),
            Column::make('service_details')->title('Service Details')->searchable(false)->orderable(false),
            Column::make('bus_pass_type')->title('Bus Pass Type')->searchable(false)->orderable(false),
            Column::make('branch_directorate')->title('Branch/Directorate')->searchable(false)->orderable(false),
            Column::make('submitted')->title('Submitted')->name('bus_pass_applications.created_at')->searchable(true),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('text-center')
                ->orderable(false)
                ->searchable(false),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'BusPassApprovals_' . date('YmdHis');
    }
}
