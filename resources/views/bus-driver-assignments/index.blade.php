@extends('adminlte::page')

@section('title', 'Bus Driver Assignments')

@section('content_header')
    <h1>Bus Driver Assignments</h1>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">

            <!-- Assignment Form -->
            <div class="col-md-4">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user-plus"></i> Assign Driver to Route</h3>
                    </div>
                    <div class="card-body">
                        <form id="assignmentForm">
                            @csrf
                            <div class="form-group">
                                <label for="driver_select">Select Driver:</label>
                                <select id="driver_select" name="driver_id" class="form-control">
                                    <option value="">Choose a driver...</option>
                                    @foreach ($availableDrivers as $driver)
                                        <option value="{{ $driver->id }}" data-regiment="{{ $driver->regiment_no }}"
                                            data-rank="{{ $driver->rank }}" data-name="{{ $driver->name }}">
                                            {{ $driver->rank }} {{ $driver->name }} ({{ $driver->regiment_no }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="route_select">Select Route:</label>
                                <select id="route_select" name="route_id" class="form-control">
                                    <option value="">Choose a route...</option>
                                    @foreach ($unassignedRoutes as $route)
                                        <option value="{{ $route->id }}" data-type="{{ $route->type }}">
                                            {{ $route->display_name ?? $route->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="assigned_date">Assignment Date:</label>
                                <input type="date" id="assigned_date" name="assigned_date" class="form-control"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="end_date">End Date (Optional):</label>
                                <input type="date" id="end_date" name="end_date" class="form-control">
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-user-plus"></i> Assign Driver to Route
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Current Assignments -->
            <div class="col-md-8">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-list"></i> Current Driver-Route Assignments</h3>
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
                                        <th>Bus</th>
                                        <th>Driver</th>
                                        <th>Regiment No</th>
                                        <th>Assignment Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($routes->filter(function($route) { return $route->driverAssignment && $route->driverAssignment->driver; }) as $route)
                                        <tr id="assignment-{{ $route->driverAssignment->id }}">
                                            <td>{{ $route->display_name ?? $route->name }}</td>
                                            <td>
                                                @if ($route->bus)
                                                    {{ $route->bus->name }}<br>
                                                    <small class="text-muted">({{ $route->bus->no }})</small>
                                                @else
                                                    <span class="text-muted">No bus assigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($route->driverAssignment && $route->driverAssignment->driver)
                                                    {{ $route->driverAssignment->driver->rank ?? 'N/A' }}<br>
                                                    <strong>{{ $route->driverAssignment->driver->name ?? 'Unknown' }}</strong>
                                                @else
                                                    <span class="text-muted">No driver</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($route->driverAssignment && $route->driverAssignment->driver)
                                                    {{ $route->driverAssignment->driver->regiment_no ?? 'N/A' }}
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>{{ $route->driverAssignment->assigned_date->format('d M Y') }}</td>
                                            <td>
                                                <span class="badge badge-success">Active</span>
                                            </td>
                                            <td>
                                                @if ($route->driverAssignment && $route->driverAssignment->driver)
                                                    <button type="button" class="btn btn-sm btn-warning unassign-btn"
                                                        data-assignment-id="{{ $route->driverAssignment->id }}"
                                                        data-route-name="{{ $route->display_name ?? $route->name }}"
                                                        data-driver-name="{{ $route->driverAssignment->driver->rank ?? 'N/A' }} {{ $route->driverAssignment->driver->name ?? 'Unknown' }}">
                                                        <i class="fas fa-user-times"></i> Unassign
                                                    </button>
                                                @else
                                                    <span class="text-muted">No action available</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr id="no-assignments">
                                            <td colspan="7" class="text-center text-muted">
                                                No driver-route assignments found
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
                        <h3 id="total-routes">{{ $routes->count() }}</h3>
                        <p>Total Routes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-road"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3 id="assigned-drivers">
                            {{ $routes->filter(function ($route) {return $route->driverAssignment;})->count() }}</h3>
                        <p>Assigned Drivers</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3 id="available-drivers">{{ $availableDrivers->count() }}</h3>
                        <p>Available Drivers</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user text-white"></i>
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
    @include('footer')


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

                let driverId = $('#driver_select').val();
                let routeId = $('#route_select').val();
                let routeType = $('#route_select option:selected').data('type');
                let assignedDate = $('#assigned_date').val();
                let endDate = $('#end_date').val();

                if (!driverId || !routeId || !assignedDate) {
                    showAlert('Please select a driver, route, and assignment date.', 'warning');
                    return;
                }

                $.ajax({
                    url: '{{ route('bus-driver-assignments.assign') }}',
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        driver_id: driverId,
                        route_id: routeId,
                        route_type: routeType,
                        assigned_date: assignedDate,
                        end_date: endDate
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
                        showAlert('An error occurred while assigning the driver.', 'error');
                    },
                    complete: function() {
                        $('#assignmentForm button[type="submit"]').prop('disabled', false)
                            .html('<i class="fas fa-user-plus"></i> Assign Driver to Route');
                    }
                });
            });

            // Unassign Button Click
            $(document).on('click', '.unassign-btn', function() {
                let assignmentId = $(this).data('assignment-id');
                let routeName = $(this).data('route-name');
                let driverName = $(this).data('driver-name');

                if (confirm(
                        `Are you sure you want to unassign driver "${driverName}" from route "${routeName}"?`
                    )) {
                    $.ajax({
                        url: '{{ route('bus-driver-assignments.unassign') }}',
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            assignment_id: assignmentId
                        },
                        beforeSend: function() {
                            $(`#assignment-${assignmentId} .unassign-btn`).prop('disabled',
                                    true)
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
                            showAlert('An error occurred while unassigning the driver.',
                                'error');
                        },
                        complete: function() {
                            $(`#assignment-${assignmentId} .unassign-btn`).prop('disabled',
                                    false)
                                .html('<i class="fas fa-user-times"></i> Unassign');
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
            $('#driver_select').val('');
            $('#route_select').val('');
            $('#assigned_date').val('{{ date('Y-m-d') }}');
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
