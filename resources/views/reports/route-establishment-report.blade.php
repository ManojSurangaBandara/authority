@extends('adminlte::page')

@section('title', 'Route Establishment Report')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1><i class="fas fa-chart-bar"></i> Route Establishment Report</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="#">Reports</a></li>
                <li class="breadcrumb-item active">Route Establishment</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-building"></i> Establishment-wise Application Counts by Route
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Route Selection Form -->
                        <form method="GET" action="{{ route('route-establishment-report.index') }}" class="mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="route_name">Select Route</label>
                                        <select name="route_name" id="route_name" class="form-control select2" required>
                                            <option value="">Choose a route...</option>
                                            <option value="all" {{ $selectedRoute == 'all' ? 'selected' : '' }}>All
                                                Routes</option>
                                            <optgroup label="Living Out Routes">
                                                @foreach ($busRoutes as $route)
                                                    <option value="{{ $route->name }}"
                                                        {{ $selectedRoute == $route->name ? 'selected' : '' }}>
                                                        {{ $route->name }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                            <optgroup label="Living In Routes">
                                                @foreach ($livingInBuses as $livingInBus)
                                                    <option value="{{ $livingInBus->name }}"
                                                        {{ $selectedRoute == $livingInBus->name ? 'selected' : '' }}>
                                                        {{ $livingInBus->name }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-search"></i> Generate Report
                                            </button>
                                            @if ($selectedRoute)
                                                <a href="{{ route('route-establishment-report.index', ['route_name' => $selectedRoute, 'export' => 'excel']) }}"
                                                    class="btn btn-success">
                                                    <i class="fas fa-download"></i> Export to Excel
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        @if ($selectedRoute)
                            <div class="mb-3">
                                <h4>Report for Route:
                                    <strong>{{ $selectedRoute === 'all' ? 'All Routes' : $selectedRoute }}</strong></h4>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover" id="routeEstablishmentTable">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th style="width: 5%;">No</th>
                                            <th style="width: 40%;">Establishment</th>
                                            <th style="width: 13%;">All</th>
                                            <th style="width: 13%;">Pending</th>
                                            <th style="width: 13%;">Approved</th>
                                            <th style="width: 13%;">Integrated</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($reportData as $index => $data)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td><strong>{{ $data['establishment'] }}</strong></td>
                                                <td class="text-center">
                                                    <span class="badge badge-primary">{{ $data['all'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-warning">{{ $data['pending'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-info">{{ $data['approved'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-success">{{ $data['integrated'] }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">
                                                    <div class="alert alert-info mb-0">
                                                        <i class="fas fa-info-circle"></i>
                                                        No data available for the selected route
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    @if ($reportData->isNotEmpty())
                                        <tfoot>
                                            <tr class="table-dark">
                                                <th colspan="2" class="text-right">Total:</th>
                                                <th class="text-center">{{ $reportData->sum('all') }}</th>
                                                <th class="text-center">{{ $reportData->sum('pending') }}</th>
                                                <th class="text-center">{{ $reportData->sum('approved') }}</th>
                                                <th class="text-center">{{ $reportData->sum('integrated') }}</th>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                Please select a route to generate the report.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('footer')

@endsection

@section('css')
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/select2-bootstrap4.min.css') }}" rel="stylesheet" />
@stop

@section('js')
    <script src="{{ asset('js/select2.min.js') }}"></script>
@stop

@push('js')
    <script>
        $(document).ready(function() {
            // Initialize Select2 for route dropdown
            $('#route_name').select2({
                theme: 'bootstrap4',
                placeholder: 'Select a route...',
                allowClear: true,
                width: '100%'
            });

            // Initialize DataTable
            @if ($selectedRoute && $reportData->isNotEmpty())
                $('#routeEstablishmentTable').DataTable({
                    "paging": true,
                    "lengthChange": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "responsive": true,
                    "pageLength": 25,
                    "lengthMenu": [10, 25, 50, 100],
                    "order": [
                        [1, 'asc']
                    ], // Order by establishment name
                    "columnDefs": [{
                            "orderable": false,
                            "targets": 0
                        }, // No column not orderable
                        {
                            "className": "text-center",
                            "targets": [2, 3, 4, 5]
                        } // Center align count columns
                    ]
                });
            @endif
        });
    </script>
@endpush
