@extends('adminlte::page')

@section('title', 'Bus Assignments')

@section('content_header')
    <h1>Bus Route Assignments</h1>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">

            <!-- Assignment Form -->
            <div class="col-md-4">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-link"></i> Assign Bus to Route</h3>
                    </div>
                    <div class="card-body">
                        <form id="assignmentForm">
                            @csrf
                            <div class="form-group">
                                <label for="bus_select">Select Bus:</label>
                                <select id="bus_select" name="bus_id" class="form-control">
                                    <option value="">Choose a bus...</option>
                                    @foreach ($unassignedBuses as $bus)
                                        <option value="{{ $bus->id }}" data-bus-no="{{ $bus->no }}"
                                            data-bus-name="{{ $bus->name }}">
                                            {{ $bus->name }} ({{ $bus->no }}) - {{ $bus->no_of_seats }} seats
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="route_select">Select Route:</label>
                                <select id="route_select" name="route_id" class="form-control">
                                    <option value="">Choose a route...</option>
                                    @foreach ($unassignedRoutes as $route)
                                        <option value="{{ $route->id }}">{{ $route->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-link"></i> Assign Bus to Route
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Current Assignments -->
            <div class="col-md-8">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-list"></i> Current Bus-Route Assignments</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" onclick="refreshAssignments()">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="assignmentsTable">
                                <thead>
                                    <tr>
                                        <th>Route Name</th>
                                        <th>Bus Name</th>
                                        <th>Bus Number</th>
                                        <th>Seats</th>
                                        <th>Total Capacity</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($routes->where('bus_id') as $route)
                                        <tr id="assignment-{{ $route->id }}">
                                            <td>{{ $route->name }}</td>
                                            <td>{{ $route->bus->name ?? 'N/A' }}</td>
                                            <td>{{ $route->bus->no ?? 'N/A' }}</td>
                                            <td>{{ $route->bus->no_of_seats ?? 'N/A' }}</td>
                                            <td>{{ $route->bus->total_capacity ?? 'N/A' }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-warning unassign-btn"
                                                    data-route-id="{{ $route->id }}"
                                                    data-route-name="{{ $route->name }}"
                                                    data-bus-name="{{ $route->bus->name ?? '' }}">
                                                    <i class="fas fa-unlink"></i> Unassign
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr id="no-assignments">
                                            <td colspan="6" class="text-center text-muted">
                                                No bus-route assignments found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mt-3">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3 id="total-buses">{{ $buses->count() }}</h3>
                        <p>Total Buses</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-bus"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3 id="assigned-buses">{{ $routes->where('bus_id')->count() }}</h3>
                        <p>Assigned Buses</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-link"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3 id="unassigned-buses">{{ $unassignedBuses->count() }}</h3>
                        <p>Unassigned Buses</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-bus text-white"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3 id="unassigned-routes">{{ $unassignedRoutes->count() }}</h3>
                        <p>Unassigned Routes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-road"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <div id="alert-container"></div>

@stop

@section('css')
    <style>
        .assignment-card {
            transition: all 0.3s ease;
        }

        .assignment-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .badge-assigned {
            background-color: #28a745;
        }

        .badge-unassigned {
            background-color: #dc3545;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Assignment Form Submit
            $('#assignmentForm').on('submit', function(e) {
                e.preventDefault();

                let busId = $('#bus_select').val();
                let routeId = $('#route_select').val();

                if (!busId || !routeId) {
                    showAlert('Please select both a bus and a route.', 'warning');
                    return;
                }

                $.ajax({
                    url: '{{ route('bus-assignments.assign') }}',
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        bus_id: busId,
                        route_id: routeId
                    },
                    beforeSend: function() {
                        $('#assignmentForm button[type="submit"]').prop('disabled', true)
                            .html('<i class="fas fa-spinner fa-spin"></i> Assigning...');
                    },
                    success: function(response) {
                        if (response.success) {
                            showAlert(response.message, 'success');
                            refreshAssignments();
                            resetForm();
                        } else {
                            showAlert(response.message, 'error');
                        }
                    },
                    error: function() {
                        showAlert('An error occurred while assigning the bus.', 'error');
                    },
                    complete: function() {
                        $('#assignmentForm button[type="submit"]').prop('disabled', false)
                            .html('<i class="fas fa-link"></i> Assign Bus to Route');
                    }
                });
            });

            // Unassign Button Click
            $(document).on('click', '.unassign-btn', function() {
                let routeId = $(this).data('route-id');
                let routeName = $(this).data('route-name');
                let busName = $(this).data('bus-name');

                if (confirm(
                    `Are you sure you want to unassign bus "${busName}" from route "${routeName}"?`)) {
                    $.ajax({
                        url: '{{ route('bus-assignments.unassign') }}',
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            route_id: routeId
                        },
                        beforeSend: function() {
                            $(`#assignment-${routeId} .unassign-btn`).prop('disabled', true)
                                .html('<i class="fas fa-spinner fa-spin"></i> Unassigning...');
                        },
                        success: function(response) {
                            if (response.success) {
                                showAlert(response.message, 'success');
                                refreshAssignments();
                            } else {
                                showAlert(response.message, 'error');
                            }
                        },
                        error: function() {
                            showAlert('An error occurred while unassigning the bus.', 'error');
                        },
                        complete: function() {
                            $(`#assignment-${routeId} .unassign-btn`).prop('disabled', false)
                                .html('<i class="fas fa-unlink"></i> Unassign');
                        }
                    });
                }
            });
        });

        function refreshAssignments() {
            location.reload();
        }

        function resetForm() {
            $('#assignmentForm')[0].reset();
            $('#bus_select').val('');
            $('#route_select').val('');
        }

        function showAlert(message, type) {
            let alertClass = 'alert-info';
            let icon = 'fas fa-info-circle';

            switch (type) {
                case 'success':
                    alertClass = 'alert-success';
                    icon = 'fas fa-check-circle';
                    break;
                case 'error':
                    alertClass = 'alert-danger';
                    icon = 'fas fa-exclamation-circle';
                    break;
                case 'warning':
                    alertClass = 'alert-warning';
                    icon = 'fas fa-exclamation-triangle';
                    break;
            }

            let alert = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="${icon}"></i> ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;

            $('#alert-container').html(alert);

            // Auto-hide after 5 seconds
            setTimeout(() => {
                $('#alert-container .alert').fadeOut();
            }, 5000);
        }
    </script>
@stop
