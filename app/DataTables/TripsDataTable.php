<?php

namespace App\DataTables;

use App\Models\Trip;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class TripsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Trip> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('escort_name', function ($row) {
                return $row->escort ? $row->escort->name : '';
            })
            ->addColumn('driver_name', function ($row) {
                return $row->driver ? $row->driver->name : '';
            })
            ->addColumn('bus_number', function ($row) {
                return $row->bus ? $row->bus->no : '';
            })
            ->addColumn('route_name', function ($row) {
                if ($row->route_type === 'living_out') {
                    return ($row->bus_route_name ?? '') . ' (Living Out)';
                } elseif ($row->route_type === 'living_in') {
                    return ($row->living_in_bus_name ?? '') . ' (Living In)';
                }
                return '';
            })
            ->addColumn('slcmp_incharge_name', function ($row) {
                return $row->slcmpIncharge ? $row->slcmpIncharge->name : '';
            })
            ->addColumn('trip_start_time_formatted', function ($row) {
                return $row->trip_start_time ? $row->trip_start_time->format('d M Y H:i') : '';
            })
            ->addColumn('trip_end_time_formatted', function ($row) {
                return $row->trip_end_time ? $row->trip_end_time->format('d M Y H:i') : '';
            })
            ->addColumn('duration', function ($row) {
                if ($row->trip_start_time && $row->trip_end_time) {
                    $duration = $row->trip_start_time->diff($row->trip_end_time);
                    return $duration->format('%H:%I:%S');
                }
                return '';
            })
            ->addColumn('onboardings_count', function ($row) {
                return $row->onboardings->count();
            })
            ->addColumn('start_location', function ($row) {
                if ($row->start_latitude && $row->start_longitude) {
                    $url = "https://www.google.com/maps?q={$row->start_latitude},{$row->start_longitude}";
                    return '<a href="' . $url . '" target="_blank" class="btn btn-xs btn-info" title="View Start Location"><i class="fas fa-map-marker-alt"></i></a>';
                }
                return 'N/A';
            })
            ->addColumn('end_location', function ($row) {
                if ($row->end_latitude && $row->end_longitude) {
                    $url = "https://www.google.com/maps?q={$row->end_latitude},{$row->end_longitude}";
                    return '<a href="' . $url . '" target="_blank" class="btn btn-xs btn-success" title="View End Location"><i class="fas fa-map-marker-alt"></i></a>';
                }
                return 'N/A';
            })
            ->filter(function ($query) {
                if ($date = request('date')) {
                    $query->whereDate('trips.trip_start_time', $date);
                }
                if ($tripType = request('trip_type')) {
                    if ($tripType == 'morning') {
                        $query->whereRaw("TIME(trips.trip_start_time) < '12:00:00'");
                    } elseif ($tripType == 'evening') {
                        $query->whereRaw("TIME(trips.trip_start_time) >= '12:00:00'");
                    }
                }
                if ($route = request('route')) {
                    if (strpos($route, 'route_') === 0) {
                        $id = substr($route, 6);
                        $query->where('bus_route_id', $id)
                            ->where('route_type', 'living_out');
                    } elseif (strpos($route, 'living_') === 0) {
                        $id = substr($route, 7);
                        $query->where('bus_route_id', $id)
                            ->where('route_type', 'living_in');
                    }
                }
            })
            ->rawColumns(['escort_name', 'driver_name', 'bus_number', 'route_name', 'slcmp_incharge_name', 'trip_start_time_formatted', 'trip_end_time_formatted', 'duration', 'onboardings_count', 'start_location', 'end_location'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Trip>
     */
    public function query(Trip $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['escort', 'driver', 'bus', 'slcmpIncharge', 'onboardings'])
            ->leftJoin('bus_routes', function ($join) {
                $join->on('trips.bus_route_id', '=', 'bus_routes.id')
                    ->where('trips.route_type', '=', 'living_out');
            })
            ->leftJoin('living_in_buses', function ($join) {
                $join->on('trips.bus_route_id', '=', 'living_in_buses.id')
                    ->where('trips.route_type', '=', 'living_in');
            })
            ->select('trips.*', 'bus_routes.name as bus_route_name', 'living_in_buses.name as living_in_bus_name')
            ->orderBy('trips.trip_start_time', 'desc');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('trips-table')
            ->columns($this->getColumns())
            ->ajax([
                'url' => route('trips.index'),
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
            Column::make('escort_name')->title('Escort')->searchable(true),
            Column::make('driver_name')->title('Driver')->searchable(true),
            Column::make('bus_number')->title('Bus')->searchable(true),
            Column::make('route_name')->title('Route')->searchable(true),
            Column::make('slcmp_incharge_name')->title('SLCMP Incharge')->searchable(true),
            Column::make('trip_start_time_formatted')->title('Start Time')->searchable(true),
            Column::make('trip_end_time_formatted')->title('End Time')->searchable(true),
            Column::make('duration')->title('Duration')->searchable(false),
            Column::make('onboardings_count')->title('Passengers')->searchable(false),
            Column::make('start_location')->title('Start Location')->searchable(false)->orderable(false),
            Column::make('end_location')->title('End Location')->searchable(false)->orderable(false),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Trips_' . date('YmdHis');
    }
}
