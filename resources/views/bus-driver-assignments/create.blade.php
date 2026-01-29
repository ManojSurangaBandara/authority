@extends('adminlte::page')

@section('title', 'Assign Bus Driver')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1><i class="fas fa-user-tie"></i> Assign Bus Driver</h1>
        </div>

    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-teal">
                    <div class="card-header">
                        <i class="nav-icon fas fa-user-tie nav-icon"></i> {{ __('Assign Bus Driver') }}
                        <div class="card-tools">
                            <a href="{{ route('bus-driver-assignments.index') }}" class="btn btn-dark btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>

                    <form action="{{ route('bus-driver-assignments.store') }}" method="POST" id="busDriverAssignmentForm">
                        @csrf
                        <div class="card-body">

                            <div class="row">
                                <!-- Bus Route Selection -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bus_route_id">Bus Route <span class="text-danger">*</span></label>
                                        <select class="form-control @error('bus_route_id') is-invalid @enderror"
                                            id="bus_route_id" name="bus_route_id" required>
                                            <option value="">Select Bus Route</option>
                                            @foreach ($busRoutes as $route)
                                                <option value="{{ $route->id }}"
                                                    {{ old('bus_route_id') == $route->id ? 'selected' : '' }}>
                                                    {{ $route->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('bus_route_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Bus No (Auto Fill) -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Bus No</label>
                                        <div class="form-control" id="bus_no_display" style="background-color: #f8f9fa;">
                                            <span class="text-muted">Select a bus route first</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Driver Regiment No (Search Box) -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="driver_regiment_no">Driver Regiment No <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text"
                                                class="form-control @error('driver_regiment_no') is-invalid @enderror"
                                                id="driver_regiment_no" name="driver_regiment_no"
                                                value="{{ old('driver_regiment_no') }}" placeholder="Enter regiment number"
                                                required>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-info" id="fetch-driver-details">
                                                    <i class="fas fa-search"></i> Search
                                                </button>
                                            </div>
                                        </div>
                                        @error('driver_regiment_no')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Enter regiment number and click search to auto-fill driver details from Strength
                                            Management System
                                        </small>
                                    </div>
                                </div>

                                <!-- Driver Rank (Auto Fill) -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="driver_rank">Driver Rank <span class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('driver_rank') is-invalid @enderror" id="driver_rank"
                                            name="driver_rank" value="{{ old('driver_rank') }}"
                                            placeholder="Auto-filled from API" readonly required>
                                        @error('driver_rank')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Driver Name (Auto Fill) -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="driver_name">Driver Name <span class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('driver_name') is-invalid @enderror" id="driver_name"
                                            name="driver_name" value="{{ old('driver_name') }}"
                                            placeholder="Auto-filled from API" readonly required>
                                        @error('driver_name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Contact Number (Auto Fill) -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="driver_contact_no">Contact Number <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('driver_contact_no') is-invalid @enderror"
                                            id="driver_contact_no" name="driver_contact_no"
                                            value="{{ old('driver_contact_no') }}"
                                            placeholder="Enter manually (not available from API)" required>
                                        @error('driver_contact_no')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Contact number must be entered manually as it is not available from the API
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Status -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status">Status <span class="text-danger">*</span></label>
                                        <select class="form-control @error('status') is-invalid @enderror" id="status"
                                            name="status" required>
                                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>
                                                Active</option>
                                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                                Inactive</option>
                                        </select>
                                        @error('status')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Assign Driver
                            </button>
                            <a href="{{ route('bus-driver-assignments.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link href="{{ asset('css/toastr.min.css') }}" rel="stylesheet">
    <style>
        /* Standard form control styling for consistency */
        #bus_route_id {
            height: calc(1.5em + 0.75rem + 2px);
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        #bus_route_id:focus {
            color: #495057;
            background-color: #fff;
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        #bus_route_id:hover {
            border-color: #adb5bd;
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Toastr configuration
            toastr.options = {
                closeButton: true,
                progressBar: true,
                timeOut: 5000
            };

            // Bus Route Selection Change
            $('#bus_route_id').on('change', function() {
                const routeId = $(this).val();
                console.log('Route ID selected:', routeId); // Debug log

                if (routeId) {
                    // Show loading state
                    $('#bus_no_display').html(
                        '<i class="fas fa-spinner fa-spin"></i> Loading bus details...');

                    // Fetch bus details for selected route
                    $.ajax({
                        url: '{{ route('bus-driver-assignments.get-bus-details') }}',
                        type: 'GET',
                        data: {
                            bus_route_id: routeId
                        },
                        success: function(response) {
                            console.log('Bus details response:', response); // Debug log

                            if (response.success && response.data) {
                                $('#bus_no_display').html(
                                    '<strong>' + response.data.bus_no + '</strong><br>' +
                                    '<small class="text-muted">' + response.data.bus_name +
                                    ' (' + response.data.bus_type + ')</small>'
                                );
                                toastr.success('Bus details loaded successfully!');
                            } else {
                                $('#bus_no_display').html(
                                    '<span class="text-muted">Bus details not available</span>'
                                );
                                toastr.warning('Bus details not found for this route.');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log('AJAX Error:', xhr.responseText); // Debug log
                            $('#bus_no_display').html(
                                '<span class="text-danger">Error loading bus details</span>'
                            );

                            let message = 'Error loading bus details.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            toastr.error(message);
                        }
                    });
                } else {
                    $('#bus_no_display').html('<span class="text-muted">Select a bus route first</span>');
                }
            });

            // Driver Search
            $('#fetch-driver-details').on('click', function() {
                const regimentNo = $('#driver_regiment_no').val().trim();

                if (!regimentNo) {
                    toastr.warning('Please enter a regiment number first.');
                    return;
                }

                const button = $(this);
                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Searching...');

                // Clear previous data
                $('#driver_rank, #driver_name, #driver_contact_no').val('');

                $.ajax({
                    url: '{{ route('bus-driver-assignments.get-driver-details') }}',
                    type: 'GET',
                    data: {
                        regiment_no: regimentNo
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#driver_rank').val(response.data.rank);
                            $('#driver_name').val(response.data.name);
                            // Clear contact field since it's not provided by API
                            $('#driver_contact_no').val('');

                            // Make the contact field editable and focused
                            $('#driver_contact_no').prop('readonly', false).focus();

                            toastr.success(
                                'Driver details loaded successfully! Please enter contact number manually.'
                            );
                        } else {
                            toastr.error(response.message || 'Driver not found.');
                        }
                    },
                    error: function(xhr) {
                        console.log('AJAX Error:', xhr.responseText); // Debug log
                        let message = 'Error loading driver details.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        toastr.error(message);
                    },
                    complete: function() {
                        button.prop('disabled', false).html(
                            '<i class="fas fa-search"></i> Search');
                    }
                });
            });

            // Allow Enter key to trigger search
            $('#driver_regiment_no').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    $('#fetch-driver-details').click();
                }
            });

            // Form validation
            $('#busDriverAssignmentForm').on('submit', function(e) {
                const rank = $('#driver_rank').val().trim();
                const name = $('#driver_name').val().trim();
                const contact = $('#driver_contact_no').val().trim();

                if (!rank || !name || !contact) {
                    e.preventDefault();
                    toastr.error('Please search and load driver details before submitting.');
                    return false;
                }
            });
        });
    </script>
@stop
