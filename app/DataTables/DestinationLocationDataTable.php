<?php

namespace App\DataTables;

use App\Models\DestinationLocation;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class DestinationLocationDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<DestinationLocation> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                // View button (always available)
                $viewBtn = '<a href="' . route('destination-locations.show', $row->id) . '" class="btn btn-xs btn-info" title="View"><i class="fas fa-eye"></i></a>';
                
                // Edit button (always enabled)
                $editBtn = '<a href="' . route('destination-locations.edit', $row->id) . '" class="btn btn-xs btn-primary mx-1" title="Edit"><i class="fas fa-edit"></i></a>';

                // Delete button (always enabled)
                $deleteBtn = '<form action="' . route('destination-locations.destroy', $row->id) . '" method="POST" style="display:inline">
                    ' . csrf_field() . '
                    ' . method_field("DELETE") . '
                    <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm(\'Are you sure you want to delete this entry?\')" title="Delete">
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
         */
        public function query(DestinationLocation $model): QueryBuilder
        {
            return $model->newQuery();
        }

        /**
         * Optional method if you want to use the html builder.
         */
        public function html(): HtmlBuilder
        {
            return $this->builder()
                        ->setTableId('destinationlocation-table')
                        ->columns($this->getColumns())
                        ->minifiedAjax()
                        ->orderBy(1)
                        ->selectStyleSingle()
                        ->buttons([
                            Button::make('create'),
                            Button::make('export'),
                            Button::make('print'),
                            Button::make('reset'),
                            Button::make('reload')
                        ]);
        }

        /**
         * Get the columns definition.
         */
        public function getColumns(): array
        {
            return [
                Column::make('DT_RowIndex')->title('#')->searchable(false)->orderable(false)->width(30),
                Column::make('destination_location')->title('Destination Location'),
                Column::computed('action')
                      ->exportable(false)
                      ->printable(false)
                      ->width(100)
                      ->addClass('text-center'),
            ];
        }

        /**
         * Get the filename for export.
         */
        protected function filename(): string
        {
            return 'DestinationLocation_' . date('YmdHis');
        }
     

}