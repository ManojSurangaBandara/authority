<?php

namespace App\DataTables;

use App\Models\BusFillingStationAssignment;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class BusFillingStationAssignmentDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<BusFillingStationAssignment> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('bus_details', function ($row) {
                return '<strong>' . ($row->bus ? $row->bus->name : 'N/A') . '</strong><br>' .
                    'No: ' . ($row->bus ? $row->bus->no : 'N/A') . '<br>' .
                    '<small class="text-muted">' . ($row->bus && $row->bus->type ? $row->bus->type->name : 'N/A') . '</small>';
            })
            ->addColumn('filling_station_name', function ($row) {
                return $row->fillingStation ? $row->fillingStation->name : 'N/A';
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
                $viewBtn = '<a href="' . route('bus-filling-station-assignments.show', $row->id) . '" class="btn btn-xs btn-info" title="View"><i class="fas fa-eye"></i></a>';
                $editBtn = '<a href="' . route('bus-filling-station-assignments.edit', $row->id) . '" class="btn btn-xs btn-primary mx-1" title="Edit"><i class="fas fa-edit"></i></a>';
                $deleteBtn = '<form action="' . route('bus-filling-station-assignments.destroy', $row->id) . '" method="POST" style="display:inline">
                    ' . csrf_field() . '
                    ' . method_field("DELETE") . '
                    <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm(\'Are you sure you want to delete this assignment?\')" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>';

                return $viewBtn . $editBtn . $deleteBtn;
            })
            ->filterColumn('bus_details', function ($query, $keyword) {
                $query->whereHas('bus', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                        ->orWhere('no', 'like', "%{$keyword}%");
                })->orWhereHas('bus.type', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('filling_station_name', function ($query, $keyword) {
                $query->whereHas('fillingStation', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['bus_details', 'status', 'action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<BusFillingStationAssignment>
     */
    public function query(BusFillingStationAssignment $model): QueryBuilder
    {
        return $model->newQuery()->with(['bus.type', 'fillingStation']);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('busfillingstation-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0)
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
            Column::make('bus_details')->title('Bus Details')->searchable(true)->orderable(false),
            Column::make('filling_station_name')->title('Filling Station')->searchable(true)->orderable(false),
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
        return 'BusFillingStationAssignment_' . date('YmdHis');
    }
}
