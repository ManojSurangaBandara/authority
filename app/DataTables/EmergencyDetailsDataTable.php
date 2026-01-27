<?php

namespace App\DataTables;

use App\Models\BusPassApplication;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class EmergencyDetailsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<BusPassApplication> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $showUrl = route('bus-pass-applications.show', $row->id) . '?from=emergency-details';
                $editUrl = route('emergency-details.edit', $row->id);
                return '<a href="' . $showUrl . '" class="btn btn-sm btn-info mr-1">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="' . $editUrl . '" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i>
                        </a>';
            })
            ->rawColumns(['action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(BusPassApplication $model): QueryBuilder
    {
        // Only show applications where emergency details are not filled
        // and belong to the current user's establishment (for branch users)
        $query = $model->newQuery()
            ->with(['person', 'establishment'])
            ->whereHas('person', function ($q) {
                $q->whereNull('blood_group')
                    ->orWhereNull('nok_name')
                    ->orWhereNull('nok_telephone_no');
            });

        // If user is a branch user, only show applications from their establishment
        if (auth()->user()->hasAnyRole(['Bus Pass Subject Clerk (Branch)', 'Staff Officer (Branch)', 'Director (Branch)'])) {
            $query->where('bus_pass_applications.establishment_id', auth()->user()->establishment_id);
        }

        return $query->orderBy('bus_pass_applications.created_at', 'desc');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('emergency-details-table')
            ->columns($this->getColumns())
            // ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(1)
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload'),
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title('#')->orderable(false)->searchable(false),
            Column::make('id')->title('Application ID'),
            Column::make('person.regiment_no')->title('Regiment No'),
            Column::make('person.rank')->title('Rank'),
            Column::make('person.name')->title('Name'),
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
        return 'EmergencyDetails_' . date('YmdHis');
    }
}
