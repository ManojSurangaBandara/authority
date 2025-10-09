<?php

namespace App\DataTables;

use App\Models\PoliceStation;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class PoliceStationDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     * 
     * @param QueryBuilder<PoliceStation> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
              ->addIndexColumn()
              ->addColumn('action',function($row){
                // View button (always available
                $viewBtn = '<a href="' . route('police-station.show', $row->id) . '" class="btn btn-xs btn-info" title="View"><i class="fas fa-eye"></i></a>';

                 // Edit button (always available)
                $editBtn = '<a href="' . route('police-station.edit', $row->id) . '" class="btn btn-xs btn-primary mx-1" title="Edit"><i class="fas fa-edit"></i></a>';

                // Delete button (always available)
                $deleteBtn = '<form action="' . route('police-station.destroy', $row->id) . '" method="POST" style="display:inline">
                    ' . csrf_field() . '
                    ' . method_field("DELETE") . '
                    <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm(\'Are you sure you want to delete this police station?\')" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>';
                return $viewBtn . $editBtn . $deleteBtn;

              })
              ->rawColumns(['action'])
              ->setRowId('id');
    }

    /**
     * Get the query source of dataTable
     * 
     * @param PoliceStation $model
     */
    public function query(PoliceStation $model): QueryBuilder
    {
        return $model->newQuery()->select('id','name');
    }

    /**
     * optional method if you want to use the html builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('police_stations')
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
     * Get the columns
     */
    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title('#')->searchable(false)->orderable(false)->width(30),
            column::make('name')->title('Police Station Name'),
            Column::computed('action')
                 ->exportable(false)
                 ->printable(false)
                 ->width(100)
                 ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export
     */
    protected function filename(): string
    {
        return 'PoliceStation_' .date('YmdHis');
    }
}