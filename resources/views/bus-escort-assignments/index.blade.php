@extends('adminlte::page')

@section('title', 'Bus Escort Assignments')

@section('content_header')
    <h1>Bus Escort Assignments</h1>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">

            <!-- Assignment Form -->
            <div class="col-md-4">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-shield-alt"></i> Assign Escort to Route</h3>
                    </div>
                    <div class="card-body">
                        <form id="assignmentForm">
                            @csrf
                            <div class="form-group">
                                <label for="escort_select">Select Escort:</label>
                                <select id="escort_select" name="escort_id" class="form-control">
                                    <option value="">Choose an escort...</option>
                                    @foreach ($availableEscorts as $escort)
                                        <option value="{{ $escort->id }}" data-regiment="{{ $escort->regiment_no }}"
                                            data-rank="{{ $escort->rank }}" data-name="{{ $escort->name }}">
                                            {{ $escort->rank }} {{ $escort->name }} ({{ $escort->regiment_no }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="route_select">Select Route:</label>
                                <select id="route_select" name="route_id" class="form-control">
                                    <option value="">Choose a route...</option>
                                    @foreach ($unassignedRoutes as $route)
                                        <option value="{{ $route->id }}" data-route-type="{{ $route->type }}">
                                            {{ $route->display_name }}
                                            @if ($route->bus)
                                                - {{ $route->bus->name }} ({{ $route->bus->no }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>



                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-shield-alt"></i> Assign Escort to Route
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Current Assignments -->
            <div class="col-md-8">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-list"></i> Current Escort-Route Assignments</h3>
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
                                        <th>Escort</th>
                                        <th>Regiment No</th>
                                        <th>Assignment Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($allAssignments as $assignment)
                                        <tr id="assignment-{{ $assignment->id }}">
                                            <td>
                                                {{ $assignment->route_name }}
                                                @if ($assignment->route_type === 'living_in')
                                                    <span class="badge badge-info ml-1">Living In</span>
                                                @else
                                                    <span class="badge badge-primary ml-1">Living Out</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($assignment->route_type === 'living_out' && $assignment->busRoute && $assignment->busRoute->assignedBus)
                                                    {{ $assignment->busRoute->assignedBus->name }}<br>
                                                    <small
                                                        class="text-muted">({{ $assignment->busRoute->assignedBus->no }})</small>
                                                @elseif ($assignment->route_type === 'living_in' && $assignment->livingInBus && $assignment->livingInBus->assignedBus)
                                                    {{ $assignment->livingInBus->assignedBus->name }}<br>
                                                    <small
                                                        class="text-muted">({{ $assignment->livingInBus->assignedBus->no }})</small>
                                                @else
                                                    <span class="text-muted">No bus assigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($assignment->escort)
                                                    {{ $assignment->escort->rank ?? 'N/A' }}<br>
                                                    <strong>{{ $assignment->escort->name ?? 'Unknown' }}</strong>
                                                @else
                                                    <span class="text-muted">No escort</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($assignment->escort)
                                                    {{ $assignment->escort->regiment_no ?? 'N/A' }}
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>{{ $assignment->assigned_date->format('d M Y') }}</td>
                                            <td>
                                                @if ($assignment->escort)
                                                    <button type="button" class="btn btn-sm btn-warning unassign-btn"
                                                        data-assignment-id="{{ $assignment->id }}"
                                                        data-route-name="{{ $assignment->route_name }}"
                                                        data-escort-name="{{ $assignment->escort->rank ?? 'N/A' }} {{ $assignment->escort->name ?? 'Unknown' }}">
                                                        <i class="fas fa-user-times"></i> Unassign
                                                    </button>
                                                @else
                                                    <span class="text-muted">No action available</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr id="no-assignments">
                                            <td colspan="6" class="text-center text-muted">
                                                No escort-route assignments found
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
                        <h3 id="assigned-escorts">{{ $allAssignments->count() }}</h3>
                        <p>Assigned Escorts</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3 id="available-escorts">{{ $availableEscorts->count() }}</h3>
                        <p>Available Escorts</p>
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

                let escortId = $('#escort_select').val();
                let routeId = $('#route_select').val();
                let routeType = $('#route_select option:selected').data('route-type');

                if (!escortId || !routeId) {
                    showAlert('Please select an escort and route.', 'warning');
                    return;
                }

                $.ajax({
                    url: '{{ route('bus-escort-assignments.assign') }}',
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        escort_id: escortId,
                        route_id: routeId,
                        route_type: routeType
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
                        showAlert('An error occurred while assigning the escort.', 'error');
                    },
                    complete: function() {
                        $('#assignmentForm button[type="submit"]').prop('disabled', false)
                            .html('<i class="fas fa-shield-alt"></i> Assign Escort to Route');
                    }
                });
            });

            // Unassign Button Click
            $(document).on('click', '.unassign-btn', function() {
                let assignmentId = $(this).data('assignment-id');
                let routeName = $(this).data('route-name');
                let escortName = $(this).data('escort-name');

                if (confirm(
                        `Are you sure you want to unassign escort "${escortName}" from route "${routeName}"?`
                    )) {
                    $.ajax({
                        url: '{{ route('bus-escort-assignments.unassign') }}',
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
                            showAlert('An error occurred while unassigning the escort.',
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
            $('#escort_select').val('');
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
