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
            ->addColumn('action', function ($row) {
                // Check if route has active assignments only (status = 'active')
                $driverAssignmentsCount = $row->driverAssignments()->where('status', 'active')->count();
                $escortAssignmentsCount = $row->escortAssignments()->where('status', 'active')->count();
                $slcmpInchargeAssignmentsCount = $row->slcmpInchargeAssignments()->where('status', 'active')->count();

                // Check if bus is assigned to this route
                $hasBusAssigned = !is_null($row->bus_id);

                $isUsed = $driverAssignmentsCount > 0 || $escortAssignmentsCount > 0 || $slcmpInchargeAssignmentsCount > 0 || $hasBusAssigned;

                // Build usage reasons array
                $usageReasons = [];
                if ($hasBusAssigned) {
                    $usageReasons[] = "Bus is assigned to this route";
                }
                if ($driverAssignmentsCount > 0) {
                    $usageReasons[] = "Has {$driverAssignmentsCount} active driver assignment(s)";
                }
                if ($escortAssignmentsCount > 0) {
                    $usageReasons[] = "Has {$escortAssignmentsCount} active escort assignment(s)";
                }
                if ($slcmpInchargeAssignmentsCount > 0) {
                    $usageReasons[] = "Has {$slcmpInchargeAssignmentsCount} active SLCMP incharge assignment(s)";
                }

                $reasonText = implode(', ', $usageReasons);

                // View button (always available)
                $viewBtn = '<a href="' . route('bus-routes.show', $row->id) . '" class="btn btn-xs btn-info" title="View"><i class="fas fa-eye"></i></a>';

                // Edit button
                if ($isUsed) {
                    $editBtn = '<span class="btn btn-xs btn-secondary mx-1 disabled" title="Cannot edit: ' . $reasonText . '" data-toggle="tooltip">
                        <i class="fas fa-edit"></i>
                    </span>';
                } else {
                    $editBtn = '<a href="' . route('bus-routes.edit', $row->id) . '" class="btn btn-xs btn-primary mx-1" title="Edit"><i class="fas fa-edit"></i></a>';
                }

                // Delete button
                if ($isUsed) {
                    $deleteBtn = '<span class="btn btn-xs btn-secondary disabled" title="Cannot delete: ' . $reasonText . '" data-toggle="tooltip">
                        <i class="fas fa-trash"></i>
                    </span>';
                } else {
                    $deleteBtn = '<form action="' . route('bus-routes.destroy', $row->id) . '" method="POST" style="display:inline">
                        ' . csrf_field() . '
                        ' . method_field("DELETE") . '
                        <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm(\'Are you sure you want to delete this route?\')" title="Delete">
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
     * @return QueryBuilder<BusRoute>
     */
    public function query(BusRoute $model): QueryBuilder
    {
        return $model->newQuery();
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
