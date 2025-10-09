<?php

namespace App\DataTables;

use App\Models\GsDivision;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class GsDivisionDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $viewBtn = '<a href="' . route('gs-devision.show', $row->id) . '" class="btn btn-xs btn-info" title="View"><i class="fas fa-eye"></i></a>';
                $editBtn = '<a href="' . route('gs-devision.edit', $row->id) . '" class="btn btn-xs btn-primary mx-1" title="Edit"><i class="fas fa-edit"></i></a>';
                $deleteBtn = '<form action="' . route('gs-devision.destroy', $row->id) . '" method="POST" style="display:inline">'
                    . csrf_field()
                    . method_field("DELETE")
                    . '<button type="submit" class="btn btn-xs btn-danger" onclick="return confirm(\'Are you sure you want to delete this Gs Division?\')" title="Delete"><i class="fas fa-trash"></i></button></form>';

                return $viewBtn . $editBtn . $deleteBtn;
            })
            ->rawColumns(['action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(GsDivision $model): QueryBuilder
    {
        return $model->newQuery()->select('id', 'name');
    }

    /**
     * Optional HTML builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('gs_divisions-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->selectStyleSingle()
            ->buttons(
                Button::make('create'),
                Button::make('export'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
            );
    }

    /**
     * Get the columns.
     */
    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title('#')->searchable(false)->orderable(false)->width(30),
            Column::make('name')->title('Gs Division Name'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('text-center'),
        ];
    }

    /**
     * Filename for export.
     */
    protected function filename(): string
    {
        return 'GsDivision_' . date('YmdHis');
    }
}
