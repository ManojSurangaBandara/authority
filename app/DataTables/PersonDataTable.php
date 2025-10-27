<?php

namespace App\DataTables;

use App\Models\Person;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class PersonDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Person> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('person_rank', function ($row) {
                return $row->rank ?: 'Not specified';
            })
            ->addColumn('action', function ($row) {
                // Check if person has bus pass applications
                $busPassApplicationsCount = $row->busPassApplications()->count();
                $isUsed = $busPassApplicationsCount > 0;

                // Build usage reason
                $reasonText = $isUsed ? "Person has {$busPassApplicationsCount} bus pass application(s)" : '';

                // View button (always available)
                $viewBtn = '<a href="' . route('persons.show', $row->id) . '" class="btn btn-xs btn-info" title="View"><i class="fas fa-eye"></i></a>';

                // Edit button (always enabled, but with warning tooltip if person is in use)
                if ($isUsed) {
                    $editBtn = '<a href="' . route('persons.edit', $row->id) . '" class="btn btn-xs btn-primary mx-1" title="Edit (Note: Regiment number cannot be changed - ' . $reasonText . ')" data-toggle="tooltip"><i class="fas fa-edit"></i></a>';
                } else {
                    $editBtn = '<a href="' . route('persons.edit', $row->id) . '" class="btn btn-xs btn-primary mx-1" title="Edit"><i class="fas fa-edit"></i></a>';
                }

                // Delete button (disabled if person is in use)
                if ($isUsed) {
                    $deleteBtn = '<span class="btn btn-xs btn-secondary disabled" title="Cannot delete: ' . $reasonText . '" data-toggle="tooltip">
                        <i class="fas fa-trash"></i>
                    </span>';
                } else {
                    $deleteBtn = '<form action="' . route('persons.destroy', $row->id) . '" method="POST" style="display:inline">
                        ' . csrf_field() . '
                        ' . method_field("DELETE") . '
                        <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm(\'Are you sure you want to delete this person?\')" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>';
                }

                // return $viewBtn . $editBtn . $deleteBtn;
                return $viewBtn;
            })
            ->rawColumns(['action', 'person_rank'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Person>
     */
    public function query(Person $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('person-table')
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
            Column::make('regiment_no')->title('Regiment No'),
            Column::make('person_rank')->title('Rank')->searchable(false),
            Column::make('name')->title('Name'),
            Column::make('unit')->title('Unit'),
            Column::make('nic')->title('NIC'),
            Column::make('army_id')->title('Army ID'),
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
        return 'Person_' . date('YmdHis');
    }
}
