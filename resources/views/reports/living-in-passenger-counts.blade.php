@extends('adminlte::page')

@section('title', 'Living In Passenger Counts Report')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1><i class="fas fa-chart-bar"></i> Living In Passenger Counts Report</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('passenger-counts.index') }}">Passenger Counts</a></li>
                <li class="breadcrumb-item active">Living In</li>
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
                            <i class="fas fa-home"></i> Living In Route-wise Passenger Counts by Personnel Type
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover" id="passengerCountsTable">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="width: 5%;">No</th>
                                        <th style="width: 25%;">Route</th>
                                        <th style="width: 12%;">Army</th>
                                        <th style="width: 12%;">Civil</th>
                                        <th style="width: 12%;">Air Force</th>
                                        <th style="width: 12%;">Navy</th>
                                        <th style="width: 12%;">Other</th>
                                        <th style="width: 10%;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($routeData as $index => $route)
                                        <tr>
                                            <td></td>
                                            <td>
                                                <strong>{{ $route['route'] }}</strong>

                                                @if (isset($route['seating_capacity']) && $route['seating_capacity'])
                                                    <br><small class="text-muted">Capacity: {{ $route['seating_capacity'] }}
                                                        seats</small>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{ $route['army'] ?? 0 }}
                                            </td>
                                            <td class="text-center">
                                                {{ $route['civil'] ?? 0 }}
                                            </td>
                                            <td class="text-center">
                                                {{ $route['air force'] ?? 0 }}
                                            </td>
                                            <td class="text-center">
                                                {{ $route['navy'] ?? 0 }}
                                            </td>
                                            <td class="text-center">
                                                {{ $route['other'] ?? 0 }}
                                            </td>
                                            <td class="text-center">
                                                <strong>{{ $route['total'] }}</strong>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">
                                                <div class="alert alert-info mb-0">
                                                    <i class="fas fa-info-circle"></i>
                                                    No living in route data available
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if ($routeData->count() > 0)
                                    <tfoot>
                                        <tr class="bg-light">
                                            <th colspan="2">Total Passengers</th>
                                            <th class="text-center">
                                                <strong>{{ $routeData->sum('army') }}</strong>
                                            </th>
                                            <th class="text-center">
                                                <strong>{{ $routeData->sum('civil') }}</strong>
                                            </th>
                                            <th class="text-center">
                                                <strong>{{ $routeData->sum('air force') }}</strong>
                                            </th>
                                            <th class="text-center">
                                                <strong>{{ $routeData->sum('navy') }}</strong>
                                            </th>
                                            <th class="text-center">
                                                <strong>{{ $routeData->sum('other') }}</strong>
                                            </th>
                                            <th class="text-center">
                                                <strong>{{ $routeData->sum('total') }}</strong>
                                            </th>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i>
                                    Report includes approved applications only
                                </small>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="button" class="btn btn-primary btn-sm" onclick="printReport()">
                                    <i class="fas fa-print"></i> Print Report
                                </button>
                                <button type="button" class="btn btn-success btn-sm" onclick="exportToExcel()">
                                    <i class="fas fa-file-excel"></i> Export to Excel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="row mt-3">
                    <div class="col-md-2">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ $routeData->sum('army') }}</h3>
                                <p>Army Personnel</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $routeData->sum('civil') }}</h3>
                                <p>Civil Personnel</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-user"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3>{{ $routeData->sum('air force') }}</h3>
                                <p>Air Force</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-plane"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="small-box bg-secondary">
                            <div class="inner">
                                <h3>{{ $routeData->sum('navy') }}</h3>
                                <p>Navy Personnel</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-anchor"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $routeData->sum('other') }}</h3>
                                <p>Other Personnel</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="small-box bg-dark">
                            <div class="inner">
                                <h3>{{ $routeData->sum('total') }}</h3>
                                <p>Total Passengers</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('footer')

@stop

@section('css')
    <style>
        .table thead th {
            border-top: none;
            font-weight: bold;
            text-align: center;
            background-color: #17a2b8 !important;
            color: white !important;
        }

        .badge {
            font-size: 0.9em;
            padding: 0.4em 0.8em;
        }

        .small-box {
            border-radius: 8px;
        }

        .small-box .inner h3 {
            font-size: 2.2rem;
            font-weight: bold;
        }

        .card-header {
            background: linear-gradient(45deg, #17a2b8, #007bff);
            color: white;
        }

        .card-header .card-title {
            font-weight: bold;
        }

        @media print {

            .card-footer,
            .breadcrumb,
            .btn {
                display: none !important;
            }

            .small-box {
                break-inside: avoid;
            }
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Initialize DataTable for better functionality
            var table = $('#passengerCountsTable').DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "paging": false,
                "info": false,
                "searching": true,
                "ordering": true,
                "order": [
                    [1, "asc"]
                ], // Sort by route name
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0
                }],
                "drawCallback": function(settings) {
                    // Update row numbers after each draw
                    var api = this.api();
                    api.column(0, {
                        page: 'current'
                    }).nodes().each(function(cell, i) {
                        cell.innerHTML = i + 1;
                    });
                }
            });
        });

        function printReport() {
            window.print();
        }

        function exportToExcel() {
            // Simple CSV export functionality
            let csv = 'No,Route,Army,Civil,Air Force,Navy,Other,Total\n';

            @foreach ($routeData as $index => $route)
                csv +=
                    '{{ $index + 1 }},"{{ $route['route'] }}",{{ $route['army'] ?? 0 }},{{ $route['civil'] ?? 0 }},{{ $route['air force'] ?? 0 }},{{ $route['navy'] ?? 0 }},{{ $route['other'] ?? 0 }},{{ $route['total'] }}\n';
            @endforeach

            // Add totals row
            csv +=
                'Total,All Routes,{{ $routeData->sum('army') }},{{ $routeData->sum('civil') }},{{ $routeData->sum('air force') }},{{ $routeData->sum('navy') }},{{ $routeData->sum('other') }},{{ $routeData->sum('total') }}\n';

            const blob = new Blob([csv], {
                type: 'text/csv'
            });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'living-in-passenger-counts-report-' + new Date().toISOString().slice(0, 10) + '.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }
    </script>
@stop
