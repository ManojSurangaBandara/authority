<?php

namespace App\DataTables;

use App\Models\Bu;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use App\Models\Bus;

class BusDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Bu> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('type.name', function ($row) {
                return $row->type ? $row->type->name : 'N/A';
            })
            ->addColumn('action', function ($row) {
                // Use the preloaded counts for better performance
                $routesCount = $row->routes_count ?? 0;
                $fillingStationAssignmentsCount = $row->filling_station_assignments_count ?? 0;
                $isUsed = $routesCount > 0 || $fillingStationAssignmentsCount > 0;

                // Build usage reasons array
                $usageReasons = [];
                if ($routesCount > 0) {
                    $usageReasons[] = "Assigned to {$routesCount} route(s)";
                }
                if ($fillingStationAssignmentsCount > 0) {
                    $usageReasons[] = "Has {$fillingStationAssignmentsCount} filling station assignment(s)";
                }

                $reasonText = implode(', ', $usageReasons);

                // View button (always available)
                $viewBtn = '<a href="' . route('buses.show', $row->id) . '" class="btn btn-xs btn-info" title="View"><i class="fas fa-eye"></i></a>';

                // Edit button (disabled if bus is assigned to routes)
                if ($routesCount > 0) {
                    $editBtn = '<span class="btn btn-xs btn-secondary disabled mx-1" title="Cannot edit: Assigned to ' . $routesCount . ' route(s)" data-toggle="tooltip">
                        <i class="fas fa-edit"></i>
                    </span>';
                } else {
                    $editBtn = '<a href="' . route('buses.edit', $row->id) . '" class="btn btn-xs btn-primary mx-1" title="Edit"><i class="fas fa-edit"></i></a>';
                }

                // Delete button (disabled if bus is in use)
                if ($isUsed) {
                    $deleteBtn = '<span class="btn btn-xs btn-secondary disabled" title="Cannot delete: ' . $reasonText . '" data-toggle="tooltip">
                        <i class="fas fa-trash"></i>
                    </span>';
                } else {
                    $deleteBtn = '<form action="' . route('buses.destroy', $row->id) . '" method="POST" style="display:inline">
                        ' . csrf_field() . '
                        ' . method_field("DELETE") . '
                        <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm(\'Are you sure you want to delete this bus?\')" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>';
                }

                return $viewBtn . $editBtn . $deleteBtn;
            })
            ->rawColumns(['action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Bus>
     */
    public function query(Bus $model): QueryBuilder
    {
        return $model->with('type')->withCount(['routes', 'fillingStationAssignments'])->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('bus-table')
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
            Column::make('no')->title('No'),
            Column::make('name')->title('Name'),
            Column::make('type.name')->title('Type'),
            Column::make('no_of_seats')->title('No of Seats'),
            Column::make('total_capacity')->title('Total Capacity'),
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
        return 'Bus_' . date('YmdHis');
    }
}
