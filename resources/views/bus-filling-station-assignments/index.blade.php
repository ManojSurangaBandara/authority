@extends('adminlte::page')

@section('title', 'Bus Filling Station Assignments')

@section('content_header')
    <h1>Bus Filling Station Assignments</h1>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">

            <!-- Assignment Form -->
            <div class="col-md-4">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-gas-pump"></i> Assign Filling Station to Bus</h3>
                    </div>
                    <div class="card-body">
                        <form id="assignmentForm">
                            @csrf
                            <div class="form-group">
                                <label for="filling_station_select">Select Filling Station:</label>
                                <select id="filling_station_select" name="filling_station_id" class="form-control">
                                    <option value="">Choose a filling station...</option>
                                    @foreach ($availableFillingStations as $fillingStation)
                                        <option value="{{ $fillingStation->id }}">
                                            {{ $fillingStation->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="bus_select">Select Bus:</label>
                                <select id="bus_select" name="bus_id" class="form-control">
                                    <option value="">Choose a bus...</option>
                                    @foreach ($unassignedBuses as $bus)
                                        <option value="{{ $bus->id }}">
                                            {{ $bus->name }}
                                            @if ($bus->no)
                                                - {{ $bus->no }}
                                            @endif
                                            @if ($bus->type)
                                                ({{ $bus->type->name }})
                                            @endif
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
                                <i class="fas fa-gas-pump"></i> Assign Filling Station to Bus
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Current Assignments -->
            <div class="col-md-8">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-list"></i> Current Filling Station-Bus Assignments</h3>
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
                                        <th>Bus Name</th>
                                        <th>Bus No</th>
                                        <th>Bus Type</th>
                                        <th>Filling Station</th>
                                        <th>Assignment Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($buses->filter(function($bus) { return $bus->fillingStationAssignment && $bus->fillingStationAssignment->fillingStation; }) as $bus)
                                        <tr id="assignment-{{ $bus->fillingStationAssignment->id }}">
                                            <td>{{ $bus->name }}</td>
                                            <td>{{ $bus->no ?? 'N/A' }}</td>
                                            <td>
                                                @if ($bus->type)
                                                    {{ $bus->type->name }}
                                                @else
                                                    <span class="text-muted">No type</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($bus->fillingStationAssignment && $bus->fillingStationAssignment->fillingStation)
                                                    <strong>{{ $bus->fillingStationAssignment->fillingStation->name ?? 'Unknown' }}</strong>
                                                @else
                                                    <span class="text-muted">No filling station</span>
                                                @endif
                                            </td>
                                            <td>{{ $bus->fillingStationAssignment->assigned_date->format('d M Y') }}</td>
                                            <td>
                                                <span class="badge badge-success">Active</span>
                                            </td>
                                            <td>
                                                @if ($bus->fillingStationAssignment && $bus->fillingStationAssignment->fillingStation)
                                                    <button type="button" class="btn btn-sm btn-warning unassign-btn"
                                                        data-assignment-id="{{ $bus->fillingStationAssignment->id }}"
                                                        data-bus-name="{{ $bus->name }} ({{ $bus->no ?? 'N/A' }})"
                                                        data-filling-station-name="{{ $bus->fillingStationAssignment->fillingStation->name ?? 'Unknown' }}">
                                                        <i class="fas fa-times"></i> Unassign
                                                    </button>
                                                @else
                                                    <span class="text-muted">No action available</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr id="no-assignments">
                                            <td colspan="7" class="text-center text-muted">
                                                No filling station-bus assignments found
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
                        <h3 id="assigned-filling-stations">
                            {{ $buses->filter(function ($bus) {return $bus->fillingStationAssignment;})->count() }}</h3>
                        <p>Assigned Filling Stations</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-gas-pump"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3 id="available-filling-stations">{{ $availableFillingStations->count() }}</h3>
                        <p>Available Filling Stations</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-gas-pump text-white"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3 id="unassigned-buses">{{ $unassignedBuses->count() }}</h3>
                        <p>Unassigned Buses</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-bus"></i>
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

                let fillingStationId = $('#filling_station_select').val();
                let busId = $('#bus_select').val();
                let assignedDate = $('#assigned_date').val();
                let endDate = $('#end_date').val();

                if (!fillingStationId || !busId || !assignedDate) {
                    showAlert('Please select a filling station, bus, and assignment date.', 'warning');
                    return;
                }

                $.ajax({
                    url: '{{ route('bus-filling-station-assignments.assign') }}',
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        filling_station_id: fillingStationId,
                        bus_id: busId,
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
                        showAlert('An error occurred while assigning the filling station.',
                            'error');
                    },
                    complete: function() {
                        $('#assignmentForm button[type="submit"]').prop('disabled', false)
                            .html(
                                '<i class="fas fa-gas-pump"></i> Assign Filling Station to Bus'
                                );
                    }
                });
            });

            // Unassign Button Click
            $(document).on('click', '.unassign-btn', function() {
                let assignmentId = $(this).data('assignment-id');
                let busName = $(this).data('bus-name');
                let fillingStationName = $(this).data('filling-station-name');

                if (confirm(
                        `Are you sure you want to unassign filling station "${fillingStationName}" from bus "${busName}"?`
                        )) {
                    $.ajax({
                        url: '{{ route('bus-filling-station-assignments.unassign') }}',
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
                            showAlert(
                                'An error occurred while unassigning the filling station.',
                                'error');
                        },
                        complete: function() {
                            $(`#assignment-${assignmentId} .unassign-btn`).prop('disabled',
                                    false)
                                .html('<i class="fas fa-times"></i> Unassign');
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
            $('#filling_station_select').val('');
            $('#bus_select').val('');
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
