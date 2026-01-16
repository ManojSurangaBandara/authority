<?php

namespace App\DataTables;

use App\Models\Onboarding;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class OnboardedPassengersDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Onboarding> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('regiment_no', function ($row) {
                return $row->busPassApplication->person->regiment_no ?? '';
            })
            ->addColumn('name', function ($row) {
                return $row->busPassApplication->person->name ?? '';
            })
            ->addColumn('rank', function ($row) {
                return $row->busPassApplication->person->rank ?? '';
            })
            ->addColumn('onboard_time', function ($row) {
                return $row->onboarded_at ? $row->onboarded_at->format('d M Y H:i') : '';
            })
            ->addColumn('action', function ($row) {
                $viewBtn = '<a href="' . route('bus-pass-applications.show', $row->bus_pass_application_id) . '" class="btn btn-xs btn-info" title="View"><i class="fas fa-eye"></i></a>';
                return $viewBtn;
            })
            ->filter(function ($query) {
                // Handle global search
                if ($search = request('search')['value'] ?? null) {
                    $query->where(function ($q) use ($search) {
                        $q->whereHas('busPassApplication.person', function ($person) use ($search) {
                            $person->where('regiment_no', 'like', "%{$search}%")
                                ->orWhere('name', 'like', "%{$search}%")
                                ->orWhere('rank', 'like', "%{$search}%");
                        });
                    });
                }

                if ($date = request('date')) {
                    $query->whereDate('onboardings.onboarded_at', $date);
                }
                if ($type = request('type')) {
                    if ($type == 'morning') {
                        $query->whereRaw("TIME(onboardings.onboarded_at) < '12:00:00'");
                    } elseif ($type == 'evening') {
                        $query->whereRaw("TIME(onboardings.onboarded_at) >= '12:00:00'");
                    }
                }
                if ($route = request('route')) {
                    if (strpos($route, 'route_') === 0) {
                        $id = substr($route, 6);
                        $query->where('onboardings.bus_route_id', $id);
                    } elseif (strpos($route, 'living_') === 0) {
                        $id = substr($route, 7);
                        $query->where('onboardings.living_in_bus_id', $id);
                    }
                }
            })
            ->rawColumns(['regiment_no', 'name', 'rank', 'onboard_time', 'action'])
            ->setRowAttr(['data-href' => function ($row) {
                return route('bus-pass-applications.show', $row->bus_pass_application_id);
            }])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Onboarding>
     */
    public function query(Onboarding $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['busPassApplication.person', 'busRoute', 'livingInBus'])
            ->orderBy('onboardings.onboarded_at', 'desc');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('onboarded-passengers-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->selectStyleSingle()
            ->dom('Bfrtip')
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
            ])
            ->ajax([
                'data' => "function(d) {
                    d.date = $('#date').val();
                    d.type = $('#type').val();
                    d.route = $('#route').val();
                }"
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title('#')->searchable(false)->orderable(false),
            Column::make('regiment_no')->title('Regiment No')->searchable(true),
            Column::make('name')->title('Name')->searchable(true),
            Column::make('rank')->title('Rank')->searchable(true),
            Column::make('onboard_time')->title('Onboard Time')->searchable(false)->orderable(false),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'OnboardedPassengers_' . date('YmdHis');
    }
}
