<?php

namespace App\DataTables;

use App\Models\BusPassApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class BusPassApplicationDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<BusPassApplication> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('regiment_no_display', function ($row) {
                if (is_null($row->person->regiment_no)) {
                    return '<span class="badge badge-success">Civil</span>';
                }
                return $row->person->regiment_no;
            })
            ->addColumn('person_rank', function ($row) {
                if (is_null($row->person->regiment_no)) {
                    return '<span class="badge badge-success">Civil</span>';
                }
                return $row->person->rank ?: 'Not specified';
            })
            ->addColumn('type_label', function ($row) {
                return $row->type_label;
            })
            ->addColumn('status_badge', function ($row) {
                return $row->status_badge;
            })
            ->addColumn('establishment_name', function ($row) {
                if ($row->establishment) {
                    return '<span class="badge badge-info">' . $row->establishment->name . '</span>';
                }
                return '<span class="text-muted">' . ($row->branch_directorate ?? 'Not specified') . '</span>';
            })
            ->addColumn('applied_date', function ($row) {
                return $row->created_at ? $row->created_at->format('d M Y') : '';
            })
            ->addColumn('action', function ($row) {
                $user = Auth::user();
                $viewBtn = '<a href="' . route('bus-pass-applications.show', $row->id) . '" class="btn btn-xs btn-info" title="View"><i class="fas fa-eye"></i></a>';

                // Check if edit and delete buttons should be shown
                $showEditDelete = false; // Default to false, only show for specific conditions

                // Only Bus Pass Subject Clerk (Branch) should be able to edit/delete applications
                if ($user) {
                    // Load user with roles for proper role checking
                    $userWithRoles = User::with('roles')->find($user->id);
                    $hasSubjectClerkRole = $userWithRoles->hasRole('Bus Pass Subject Clerk (Branch)');

                    if ($hasSubjectClerkRole) {
                        // Show edit/delete only if status is 'pending_subject_clerk' (before forwarding)
                        if ($row->status === 'pending_subject_clerk') {
                            $showEditDelete = true;
                        }
                    }
                }

                $editBtn = '';
                $deleteBtn = '';

                if ($showEditDelete) {
                    $editBtn = '<a href="' . route('bus-pass-applications.edit', $row->id) . '" class="btn btn-xs btn-primary mx-1" title="Edit"><i class="fas fa-edit"></i></a>';
                    $deleteBtn = '<form action="' . route('bus-pass-applications.destroy', $row->id) . '" method="POST" style="display:inline">
                        ' . csrf_field() . '
                        ' . method_field("DELETE") . '
                        <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm(\'Are you sure you want to delete this application?\')" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>';
                }

                return $viewBtn . $editBtn . $deleteBtn;
            })
            ->rawColumns(['action', 'status_badge', 'type_label', 'applied_date', 'establishment_name', 'person_rank', 'regiment_no_display'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<BusPassApplication>
     */
    public function query(BusPassApplication $model): QueryBuilder
    {
        $query = $model->newQuery()->with(['person', 'establishment']);

        // Filter by establishment for branch users
        $user = Auth::user();
        if ($user && $user->establishment_id) {
            // Check if user has branch roles
            $branchRoles = [
                'Bus Pass Subject Clerk (Branch)',
                'Staff Officer (Branch)',
                'Director (Branch)'
            ];

            // Load user with roles for proper role checking
            $userWithRoles = User::with('roles')->find($user->id);
            $hasBranchRole = $userWithRoles->hasAnyRole($branchRoles);

            if ($hasBranchRole) {
                $query->where('establishment_id', $user->establishment_id);
            }
        }

        // Handle filters for DMOV users
        if ($user) {
            // Load user with roles for proper role checking
            $userWithRoles = User::with('roles')->find($user->id);
            if ($userWithRoles && $userWithRoles->isMovementUser()) {
                // Filter by establishment if provided
                if (request()->has('establishment_filter') && request('establishment_filter')) {
                    $query->where('establishment_id', request('establishment_filter'));
                }

                // Filter by status if provided
                if (request()->has('status_filter') && request('status_filter')) {
                    $query->where('status', request('status_filter'));
                }
            }
        }

        return $query->orderBy('bus_pass_applications.created_at', 'desc');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('bus-pass-application-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title('#')->searchable(false)->orderable(false),
            Column::make('regiment_no_display')->title('Regiment No')->name('person.regiment_no')->searchable(true)->orderable(false),
            Column::make('person.name')->title('Name')->name('person.name'),
            Column::make('person_rank')->title('Rank')->searchable(false)->orderable(false),
            Column::make('establishment_name')->title('Branch/Directorate')->searchable(false)->orderable(false),
            Column::make('type_label')->title('Pass Type')->searchable(false),
            Column::make('status_badge')->title('Status')->searchable(false)->orderable(false),
            Column::make('applied_date')->title('Applied Date')->searchable(false)->orderable(false),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'BusPassApplication_' . date('YmdHis');
    }
}
