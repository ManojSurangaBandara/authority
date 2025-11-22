@extends('adminlte::page')

@section('title', 'Establishment wise Applications Report')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1><i class="fas fa-building"></i> Establishment wise Applications Report</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="#">Reports</a></li>
                <li class="breadcrumb-item active">Establishment wise Applications</li>
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
                            <i class="fas fa-building"></i> Applications by Establishment
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filter Form -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <form method="GET" action="{{ route('establishment-wise-applications.index') }}">
                                    <div class="form-group">
                                        <label for="establishment_id">Select Establishment:</label>
                                        <select name="establishment_id" id="establishment_id" class="form-control"
                                            onchange="this.form.submit()">
                                            <option value="">All Establishments</option>
                                            @foreach ($establishments as $establishment)
                                                <option value="{{ $establishment->id }}"
                                                    {{ $selectedEstablishment == $establishment->id ? 'selected' : '' }}>
                                                    {{ $establishment->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-8 text-right">
                                <button type="button" class="btn btn-primary btn-sm" onclick="printReport()">
                                    <i class="fas fa-print"></i> Print Report
                                </button>
                                <button type="button" class="btn btn-success btn-sm" onclick="exportToExcel()">
                                    <i class="fas fa-file-excel"></i> Export to Excel
                                </button>
                            </div>
                        </div>

                        <!-- Applications Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover"
                                id="establishmentApplicationsTable">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="width: 5%;">No</th>
                                        <th style="width: 15%;">Establishment</th>
                                        <th style="width: 10%;">Regiment No</th>
                                        <th style="width: 10%;">Rank</th>
                                        <th style="width: 15%;">Name</th>
                                        <th style="width: 15%;">Bus Route</th>
                                        <th style="width: 15%;">Destination</th>
                                        <th style="width: 3%;">Army</th>
                                        <th style="width: 3%;">Civil</th>
                                        <th style="width: 3%;">Air Force</th>
                                        <th style="width: 3%;">Navy</th>
                                        <th style="width: 3%;">Other</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($applications as $index => $application)
                                        <tr>
                                            <td></td>
                                            <td>
                                                <strong>{{ $application->establishment->name ?? 'N/A' }}</strong>
                                            </td>
                                            <td>{{ $application->person->regiment_no ?? 'N/A' }}</td>
                                            <td>{{ $application->person->rank ?? 'N/A' }}</td>
                                            <td>{{ $application->person->name ?? 'N/A' }}</td>
                                            <td>
                                                @if ($application->requested_bus_name)
                                                    <span
                                                        class="badge badge-primary">{{ $application->requested_bus_name }}</span>
                                                @endif
                                                @if ($application->weekend_bus_name)
                                                    @if ($application->requested_bus_name)
                                                        <br>
                                                    @endif
                                                    <span
                                                        class="badge badge-secondary mt-1">{{ $application->weekend_bus_name }}</span>
                                                @endif
                                                @if ($application->living_in_bus)
                                                    @if ($application->requested_bus_name || $application->weekend_bus_name)
                                                        <br>
                                                    @endif
                                                    <span class="badge badge-info mt-1">{{ $application->living_in_bus }}
                                                        (Living In)</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($application->requested_bus_name && $application->destination_from_ahq)
                                                    <span
                                                        class="badge badge-primary">{{ $application->destination_from_ahq }}</span>
                                                @endif
                                                @if ($application->weekend_bus_name && $application->weekend_destination)
                                                    @if ($application->requested_bus_name && $application->destination_from_ahq)
                                                        <br>
                                                    @endif
                                                    <span
                                                        class="badge badge-secondary mt-1">{{ $application->weekend_destination }}</span>
                                                @endif
                                                @if ($application->living_in_bus && $application->destination_location_ahq)
                                                    @if (
                                                        ($application->requested_bus_name && $application->destination_from_ahq) ||
                                                            ($application->weekend_bus_name && $application->weekend_destination))
                                                        <br>
                                                    @endif
                                                    <span
                                                        class="badge badge-info mt-1">{{ $application->destination_location_ahq }}</span>
                                                @endif
                                                @if (
                                                    !$application->destination_from_ahq &&
                                                        !$application->weekend_destination &&
                                                        !$application->destination_location_ahq)
                                                    N/A
                                                @endif
                                            </td>

                                            <!-- Person Type Columns -->
                                            @php
                                                $personTypeName = strtolower(
                                                    $application->person->personType->name ?? '',
                                                );
                                            @endphp

                                            <td class="text-center">
                                                @if ($personTypeName === 'army')
                                                    <i class="fas fa-check text-success"></i>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($personTypeName === 'civil')
                                                    <i class="fas fa-check text-success"></i>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($personTypeName === 'air force')
                                                    <i class="fas fa-check text-success"></i>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($personTypeName === 'navy')
                                                    <i class="fas fa-check text-success"></i>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($personTypeName === 'other')
                                                    <i class="fas fa-check text-success"></i>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="12" class="text-center">
                                                <div class="alert alert-info mb-0">
                                                    <i class="fas fa-info-circle"></i>
                                                    No applications found
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if ($applications->count() > 0)
                                    <tfoot>
                                        <tr class="bg-light font-weight-bold">
                                            <th colspan="7">Total Applications</th>
                                            <th class="text-center">
                                                <span class="badge badge-success">{{ $totals['army'] ?? 0 }}</span>
                                            </th>
                                            <th class="text-center">
                                                <span class="badge badge-info">{{ $totals['civil'] ?? 0 }}</span>
                                            </th>
                                            <th class="text-center">
                                                <span class="badge badge-primary">{{ $totals['air force'] ?? 0 }}</span>
                                            </th>
                                            <th class="text-center">
                                                <span class="badge badge-secondary">{{ $totals['navy'] ?? 0 }}</span>
                                            </th>
                                            <th class="text-center">
                                                <span class="badge badge-warning">{{ $totals['other'] ?? 0 }}</span>
                                            </th>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Summary Cards -->
                @if ($applications->count() > 0)
                    <div class="row mt-3">
                        <div class="col-md-2">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $totals['army'] ?? 0 }}</h3>
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
                                    <h3>{{ $totals['civil'] ?? 0 }}</h3>
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
                                    <h3>{{ $totals['air force'] ?? 0 }}</h3>
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
                                    <h3>{{ $totals['navy'] ?? 0 }}</h3>
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
                                    <h3>{{ $totals['other'] ?? 0 }}</h3>
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
                                    <h3>{{ $applications->count() }}</h3>
                                    <p>Total Applications</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-chart-bar"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .table thead th {
            border-top: none;
            font-weight: bold;
            text-align: center;
        }

        .badge {
            font-size: 0.8em;
            padding: 0.3em 0.6em;
        }

        .small-box {
            border-radius: 8px;
        }

        .small-box .inner h3 {
            font-size: 2.2rem;
            font-weight: bold;
        }

        .card-header {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
        }

        .card-header .card-title {
            font-weight: bold;
        }

        .form-group label {
            font-weight: bold;
        }

        @media print {

            .card-footer,
            .breadcrumb,
            .btn,
            .form-group {
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
            // Initialize DataTable
            var table = $('#establishmentApplicationsTable').DataTable({
                "responsive": true,
                "lengthChange": true,
                "autoWidth": false,
                "paging": true,
                "info": true,
                "searching": true,
                "ordering": true,
                "order": [
                    [1, "asc"]
                ], // Sort by establishment name
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
            let csv = 'No,Establishment,Regiment No,Rank,Name,Bus Route,Destination,Army,Civil,Air Force,Navy,Other\n';

            @foreach ($applications as $index => $application)
                @php
                    $busRoutes = array_filter([$application->requested_bus_name, $application->weekend_bus_name, $application->living_in_bus]);
                    $destinations = array_filter([$application->destination_from_ahq, $application->destination_location_ahq, $application->weekend_destination]);
                @endphp
                csv +=
                    '{{ $index + 1 }},"{{ $application->establishment->name ?? 'N/A' }}","{{ $application->person->regiment_no ?? 'N/A' }}","{{ $application->person->rank ?? 'N/A' }}","{{ $application->person->name ?? 'N/A' }}","{{ implode(', ', $busRoutes) }}","{{ implode(', ', $destinations) ?: 'N/A' }}",';

                @php
                    $personTypeName = strtolower($application->person->personType->name ?? '');
                @endphp

                csv += '{{ $personTypeName === 'army' ? '1' : '0' }},';
                csv += '{{ $personTypeName === 'civil' ? '1' : '0' }},';
                csv += '{{ $personTypeName === 'air force' ? '1' : '0' }},';
                csv += '{{ $personTypeName === 'navy' ? '1' : '0' }},';
                csv += '{{ $personTypeName === 'other' ? '1' : '0' }}\n';
            @endforeach

            // Add totals row
            csv +=
                'Total,,,,,,,"{{ $totals['army'] ?? 0 }}","{{ $totals['civil'] ?? 0 }}","{{ $totals['air force'] ?? 0 }}","{{ $totals['navy'] ?? 0 }}","{{ $totals['other'] ?? 0 }}"\n';

            const blob = new Blob([csv], {
                type: 'text/csv'
            });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'establishment-wise-applications-' + new Date().toISOString().slice(0, 10) + '.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }
    </script>
@stop
