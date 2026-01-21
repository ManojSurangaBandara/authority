<?php

namespace App\DataTables;

use App\Models\Incident;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class IncidentReportsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Incident> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('incident_type_name', function ($row) {
                return $row->incidentType->name ?? '';
            })
            ->addColumn('escort_name', function ($row) {
                return $row->escort->name ?? '';
            })
            ->addColumn('route_name', function ($row) {
                if ($row->route_type === 'living_out') {
                    return $row->bus_route_name ?? '';
                } elseif ($row->route_type === 'living_in') {
                    return $row->living_in_bus_name ?? '';
                }
                return '';
            })
            ->addColumn('driver_name', function ($row) {
                return $row->driver->name ?? '';
            })
            ->addColumn('bus_number', function ($row) {
                return $row->bus->no ?? '';
            })
            ->addColumn('reported_at', function ($row) {
                return $row->created_at ? $row->created_at->format('d M Y H:i') : '';
            })
            ->addColumn('images_count', function ($row) {
                $count = 0;
                if ($row->image1) $count++;
                if ($row->image2) $count++;
                if ($row->image3) $count++;
                return $count;
            })
            ->addColumn('action', function ($row) {
                $viewBtn = '<a href="' . route('incident-reports.show', $row->id) . '" class="btn btn-xs btn-info" title="View"><i class="fas fa-eye"></i></a>';
                return $viewBtn;
            })
            ->filter(function ($query) {
                if ($date = request('date')) {
                    $query->whereDate('incidents.created_at', $date);
                }
                if ($type = request('type')) {
                    $query->where('incidents.incident_type_id', $type);
                }
                if ($tripType = request('trip_type')) {
                    if ($tripType == 'morning') {
                        $query->whereRaw("TIME(incidents.created_at) < '12:00:00'");
                    } elseif ($tripType == 'evening') {
                        $query->whereRaw("TIME(incidents.created_at) >= '12:00:00'");
                    }
                }
                if ($route = request('route')) {
                    if (strpos($route, 'route_') === 0) {
                        $id = substr($route, 6);
                        $query->where('incidents.bus_route_id', $id)
                            ->where('incidents.route_type', 'living_out');
                    } elseif (strpos($route, 'living_') === 0) {
                        $id = substr($route, 7);
                        $query->where('incidents.bus_route_id', $id)
                            ->where('incidents.route_type', 'living_in');
                    }
                }
            })
            ->rawColumns(['incident_type_name', 'escort_name', 'route_name', 'driver_name', 'bus_number', 'reported_at', 'images_count', 'action'])
            ->setRowAttr(['data-href' => function ($row) {
                return route('incident-reports.show', $row->id);
            }])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Incident>
     */
    public function query(Incident $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['incidentType', 'escort', 'driver', 'bus', 'slcmpIncharge'])
            ->leftJoin('bus_routes', function ($join) {
                $join->on('incidents.bus_route_id', '=', 'bus_routes.id')
                    ->where('incidents.route_type', '=', 'living_out');
            })
            ->leftJoin('living_in_buses', function ($join) {
                $join->on('incidents.bus_route_id', '=', 'living_in_buses.id')
                    ->where('incidents.route_type', '=', 'living_in');
            })
            ->select('incidents.*', 'bus_routes.name as bus_route_name', 'living_in_buses.name as living_in_bus_name')
            ->orderBy('incidents.created_at', 'desc');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('incident-reports-table')
            ->columns($this->getColumns())
            ->ajax([
                'url' => route('incident-reports.index'),
                'type' => 'GET',
            ])
            ->serverSide(true)
            ->processing(true)
            ->orderBy(1);
    }

    /**
     * Get the columns.
     */
    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title('#')->searchable(false)->orderable(false)->width(30),
            Column::make('incident_type_name')->title('Incident Type'),
            Column::make('description')->title('Description'),
            Column::make('escort_name')->title('Escort'),
            Column::make('route_name')->title('Route'),
            Column::make('driver_name')->title('Driver'),
            Column::make('bus_number')->title('Bus'),
            Column::make('reported_at')->title('Reported At'),
            Column::make('images_count')->title('Images'),
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
        return 'IncidentReports_' . date('YmdHis');
    }
}
