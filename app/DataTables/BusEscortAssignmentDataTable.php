<?php

namespace App\DataTables;

use App\Models\BusEscortAssignment;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class BusEscortAssignmentDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<BusEscortAssignment> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('bus_route_name', function ($row) {
                return $row->busRoute ? $row->busRoute->name : 'N/A';
            })
            ->addColumn('bus_no', function ($row) {
                return $row->busRoute && $row->busRoute->bus ? $row->busRoute->bus->no : 'N/A';
            })
            ->addColumn('escort_details', function ($row) {
                return '<strong>' . $row->escort_regiment_no . '</strong><br>' .
                    $row->escort_rank . ' ' . $row->escort_name . '<br>' .
                    '<small class="text-muted">' . $row->escort_contact_no . '</small>';
            })
            ->addColumn('assignment_period', function ($row) {
                $period = $row->assigned_date->format('d M Y');
                if ($row->end_date) {
                    $period .= ' to ' . $row->end_date->format('d M Y');
                } else {
                    $period .= ' (Ongoing)';
                }
                return $period;
            })
            ->addColumn('status', function ($row) {
                return $row->status_badge;
            })
            ->addColumn('action', function ($row) {
                $viewBtn = '<a href="' . route('bus-escort-assignments.show', $row->id) . '" class="btn btn-xs btn-info" title="View"><i class="fas fa-eye"></i></a>';
                $editBtn = '<a href="' . route('bus-escort-assignments.edit', $row->id) . '" class="btn btn-xs btn-primary mx-1" title="Edit"><i class="fas fa-edit"></i></a>';
                $deleteBtn = '<form action="' . route('bus-escort-assignments.destroy', $row->id) . '" method="POST" style="display:inline">
                    ' . csrf_field() . '
                    ' . method_field("DELETE") . '
                    <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm(\'Are you sure you want to delete this assignment?\')" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>';

                return $viewBtn . $editBtn . $deleteBtn;
            })
            ->filterColumn('bus_route_name', function($query, $keyword) {
                $query->whereHas('busRoute', function($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('bus_no', function($query, $keyword) {
                $query->whereHas('busRoute.bus', function($q) use ($keyword) {
                    $q->where('no', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('escort_details', function($query, $keyword) {
                $query->where(function($q) use ($keyword) {
                    $q->where('escort_regiment_no', 'like', "%{$keyword}%")
                      ->orWhere('escort_rank', 'like', "%{$keyword}%")
                      ->orWhere('escort_name', 'like', "%{$keyword}%")
                      ->orWhere('escort_contact_no', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['escort_details', 'status', 'action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<BusEscortAssignment>
     */
    public function query(BusEscortAssignment $model): QueryBuilder
    {
        return $model->newQuery()->with(['busRoute.bus']);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('busescortassignment-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0)
            ->selectStyleSingle()
            ->parameters([
                'processing' => false,
                'serverSide' => true,
                'responsive' => true,
                'autoWidth' => false,
                'searching' => true,
                'paging' => true,
                'info' => true,
                'lengthChange' => true,
                'pageLength' => 25,
                'language' => [
                    'processing' => '',
                    'loadingRecords' => '',
                    'zeroRecords' => 'No escort assignments found',
                    'emptyTable' => 'No escort assignments available'
                ]
            ])
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
            Column::make('bus_route_name')->title('Bus Route')->searchable(true)->orderable(false),
            Column::make('bus_no')->title('Bus No')->searchable(true)->orderable(false),
            Column::make('escort_details')->title('Escort Details')->searchable(true)->orderable(false),
            Column::make('assignment_period')->title('Assignment Period')->searchable(false)->orderable(false),
            Column::make('status')->title('Status')->searchable(false)->orderable(false),
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
        return 'BusEscortAssignment_' . date('YmdHis');
    }
}
