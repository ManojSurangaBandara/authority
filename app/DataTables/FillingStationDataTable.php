<?php

namespace App\DataTables;

use App\Models\FillingStation;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class FillingStationDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<FillingStation> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                // Check if filling station has active assignments
                $activeAssignmentsCount = $row->fillingStationAssignments()->where('status', 'active')->count();
                $isUsed = $activeAssignmentsCount > 0;

                // Build usage reason
                $reasonText = $isUsed ? "Filling station has {$activeAssignmentsCount} active assignment(s)" : '';

                // View button (always available)
                $viewBtn = '<a href="' . route('filling-stations.show', $row->id) . '" class="btn btn-xs btn-info" title="View"><i class="fas fa-eye"></i></a>';

                // Edit button (disabled if filling station is in use)
                if ($isUsed) {
                    $editBtn = '<span class="btn btn-xs btn-secondary mx-1 disabled" title="Cannot edit: ' . $reasonText . '" data-toggle="tooltip">
                        <i class="fas fa-edit"></i>
                    </span>';
                } else {
                    $editBtn = '<a href="' . route('filling-stations.edit', $row->id) . '" class="btn btn-xs btn-primary mx-1" title="Edit"><i class="fas fa-edit"></i></a>';
                }

                // Delete button (disabled if filling station is in use)
                if ($isUsed) {
                    $deleteBtn = '<span class="btn btn-xs btn-secondary disabled" title="Cannot delete: ' . $reasonText . '" data-toggle="tooltip">
                        <i class="fas fa-trash"></i>
                    </span>';
                } else {
                    $deleteBtn = '<form action="' . route('filling-stations.destroy', $row->id) . '" method="POST" style="display:inline">
                        ' . csrf_field() . '
                        ' . method_field("DELETE") . '
                        <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm(\'Are you sure you want to delete this filling station?\')" title="Delete">
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
     * @return QueryBuilder<FillingStation>
     */
    public function query(FillingStation $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('filling-station-table')
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
            Column::make('name')->title('Name'),
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
        return 'FillingStation_' . date('YmdHis');
    }
}
