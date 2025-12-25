<?php

namespace App\DataTables;

use App\Models\BusPassApplication;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class QrDownloadDataTable extends DataTable
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
            ->addColumn('type_label', function ($row) {
                return $row->type_label;
            })
            ->addColumn('status_badge', function ($row) {
                return $row->status_badge;
            })
            ->addColumn('applied_date', function ($row) {
                return $row->created_at ? $row->created_at->format('d M Y') : '';
            })
            ->addColumn('person_rank', function ($row) {
                return $row->person ? $row->person->rank : '';
            })
            ->addColumn('action', function ($row) {
                $viewBtn = '<a href="' . route('bus-pass-applications.show', $row->id) . '" class="btn btn-xs btn-info" title="View"><i class="fas fa-eye"></i></a>';
                $downloadQrBtn = '<a href="' . route('qr-download.download', $row->id) . '" class="btn btn-xs btn-success mx-1" title="Download QR" target="_blank"><i class="fas fa-qrcode"></i></a>';

                return $viewBtn . $downloadQrBtn;
            })
            ->filter(function ($query) {
                // Apply establishment filter if provided
                if ($estId = request('establishment_id')) {
                    $query->where('establishment_id', $estId);
                }

                // Handle global search (server-side) for related fields
                $searchValue = request('search')['value'] ?? null;
                if ($searchValue) {
                    $query->where(function ($q) use ($searchValue) {
                        $q->whereHas('person', function ($p) use ($searchValue) {
                            $p->where('regiment_no', 'like', "%{$searchValue}%")
                                ->orWhere('name', 'like', "%{$searchValue}%");
                        });

                        $q->orWhereHas('establishment', function ($e) use ($searchValue) {
                            $e->where('name', 'like', "%{$searchValue}%");
                        });

                        // also allow searching by application id as fallback
                        $q->orWhere('bus_pass_applications.id', 'like', "%{$searchValue}%");
                    });
                }
            })
            ->rawColumns(['action', 'status_badge', 'type_label', 'applied_date', 'person_rank'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<BusPassApplication>
     */
    public function query(BusPassApplication $model): QueryBuilder
    {
        $query = $model->newQuery()->with(['person', 'establishment'])->where('status', 'integrated_to_temp_card');

        // Filter by establishment for branch users, but not for DMOV users
        $user = Auth::user();
        $branchRoles = ['Bus Pass Subject Clerk (Branch)', 'Staff Officer (Branch)', 'Director (Branch)'];
        $dmovRoles = [
            'System Administrator (DMOV)',
            'Subject Clerk (DMOV)',
            'Staff Officer 2 (DMOV)',
            'Staff Officer 1 (DMOV)',
            'Col Mov (DMOV)',
            'Director (DMOV)',
            'Bus Escort (DMOV)'
        ];

        if ($user && $user->hasAnyRole($branchRoles) && $user->establishment_id && !$user->hasAnyRole($dmovRoles)) {
            $query->where('establishment_id', $user->establishment_id);
        }

        return $query->orderBy('bus_pass_applications.created_at', 'desc');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('qr-download-table')
            ->columns($this->getColumns())
            ->ajax([
                'url' => route('qr-download.index'),
                'data' => "function(d) {
                    var establishmentId = $('#establishment_id').val();
                    if (establishmentId) {
                        d.establishment_id = establishmentId;
                    }
                }"
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title('#')->searchable(false)->orderable(false),
            Column::make('person.regiment_no')->title('Regiment No')->name('person.regiment_no'),
            Column::make('person.name')->title('Name')->name('person.name'),
            Column::make('person_rank')->title('Rank')->searchable(true),
            Column::make('establishment.name')->title('Establishment')->name('establishment.name'),
            Column::make('type_label')->title('Pass Type')->searchable(true),
            Column::make('status_badge')->title('Status')->searchable(true)->orderable(false),
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
        return 'QrDownload_' . date('YmdHis');
    }
}
