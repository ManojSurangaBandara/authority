<?php

namespace App\DataTables;

use App\Models\BusRoute;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class BusRouteDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<BusRoute> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('bus.name', function ($row) {
                return $row->bus ? $row->bus->name : 'N/A';
            })
            ->addColumn('bus.type.name', function ($row) {
                return $row->bus && $row->bus->type ? $row->bus->type->name : 'N/A';
            })
            ->addColumn('bus.no_of_seats', function ($row) {
                return $row->bus ? $row->bus->no_of_seats : 'N/A';
            })
            ->addColumn('action', function ($row) {
                $viewBtn = '<a href="' . route('bus-routes.show', $row->id) . '" class="btn btn-xs btn-info" title="View"><i class="fas fa-eye"></i></a>';
                $editBtn = '<a href="' . route('bus-routes.edit', $row->id) . '" class="btn btn-xs btn-primary mx-1" title="Edit"><i class="fas fa-edit"></i></a>';
                $deleteBtn = '<form action="' . route('bus-routes.destroy', $row->id) . '" method="POST" style="display:inline">
                    ' . csrf_field() . '
                    ' . method_field("DELETE") . '
                    <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm(\'Are you sure you want to delete this route?\')" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>';

                return $viewBtn . $editBtn . $deleteBtn;
            })
            ->rawColumns(['action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<BusRoute>
     */
    public function query(BusRoute $model): QueryBuilder
    {
        return $model->with('bus.type')->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('busroute-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
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
            Column::make('name')->title('Route Name'),
            Column::make('bus.name')->title('Bus Name'),
            Column::make('bus.no')->title('Bus No'),
            Column::make('bus.type.name')->title('Bus Type'),
            Column::make('bus.no_of_seats')->title('No of Seats'),
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
        return 'BusRoute_' . date('YmdHis');
    }
}
