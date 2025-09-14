@extends('adminlte::page')

@section('title', 'Edit SLCMP In-charge Assignment')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1><i class="fas fa-shield-alt"></i> Edit SLCMP In-charge Assignment</h1>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-teal">
                    <div class="card-header">
                        <i class="nav-icon fas fa-shield-alt nav-icon"></i> {{ __('Edit SLCMP In-charge Assignment') }}
                        <div class="card-tools">
                            <a href="{{ route('slcmp-incharge-assignments.index') }}" class="btn btn-dark btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>

                    <form action="{{ route('slcmp-incharge-assignments.update', $slcmpInchargeAssignment->id) }}" method="POST" id="slcmpAssignmentForm">
                        @csrf
                        @method('PUT')
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
                                                    {{ (old('bus_route_id', $slcmpInchargeAssignment->bus_route_id) == $route->id) ? 'selected' : '' }}>
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
                                            @if($slcmpInchargeAssignment->busRoute && $slcmpInchargeAssignment->busRoute->bus)
                                                <strong>{{ $slcmpInchargeAssignment->busRoute->bus->no }}</strong><br>
                                                <small class="text-muted">{{ $slcmpInchargeAssignment->busRoute->bus->name ?? '' }} ({{ $slcmpInchargeAssignment->busRoute->bus->type->name ?? '' }})</small>
                                            @else
                                                <span class="text-muted">Select a bus route first</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- SLCMP Regiment No (Search Box) -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="slcmp_regiment_no">SLCMP Regiment No <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text"
                                                class="form-control @error('slcmp_regiment_no') is-invalid @enderror"
                                                id="slcmp_regiment_no" name="slcmp_regiment_no"
                                                value="{{ old('slcmp_regiment_no', $slcmpInchargeAssignment->slcmp_regiment_no) }}"
                                                placeholder="Enter regiment number" required>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-info" id="fetch-slcmp-details">
                                                    <i class="fas fa-search"></i> Search
                                                </button>
                                            </div>
                                        </div>
                                        @error('slcmp_regiment_no')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Enter regiment number and click search to auto-fill SLCMP details from Strength
                                            Management System
                                        </small>
                                    </div>
                                </div>

                                <!-- SLCMP Rank (Auto Fill) -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="slcmp_rank">SLCMP Rank <span class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('slcmp_rank') is-invalid @enderror" id="slcmp_rank"
                                            name="slcmp_rank" value="{{ old('slcmp_rank', $slcmpInchargeAssignment->slcmp_rank) }}"
                                            placeholder="Auto-filled from API" readonly required>
                                        @error('slcmp_rank')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- SLCMP Name (Auto Fill) -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="slcmp_name">SLCMP Name <span class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('slcmp_name') is-invalid @enderror" id="slcmp_name"
                                            name="slcmp_name" value="{{ old('slcmp_name', $slcmpInchargeAssignment->slcmp_name) }}"
                                            placeholder="Auto-filled from API" readonly required>
                                        @error('slcmp_name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- SLCMP Contact No (Manual Entry) -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="slcmp_contact_no">SLCMP Contact No <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('slcmp_contact_no') is-invalid @enderror"
                                            id="slcmp_contact_no" name="slcmp_contact_no"
                                            value="{{ old('slcmp_contact_no', $slcmpInchargeAssignment->slcmp_contact_no) }}"
                                            placeholder="Enter contact number" required>
                                        @error('slcmp_contact_no')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Contact number needs to be filled manually
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Assigned Date -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="assigned_date">Assigned Date <span class="text-danger">*</span></label>
                                        <input type="date"
                                            class="form-control @error('assigned_date') is-invalid @enderror"
                                            id="assigned_date" name="assigned_date"
                                            value="{{ old('assigned_date', $slcmpInchargeAssignment->assigned_date ? $slcmpInchargeAssignment->assigned_date->format('Y-m-d') : '') }}" required>
                                        @error('assigned_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- End Date -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="end_date">End Date (Optional)</label>
                                        <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                            id="end_date" name="end_date"
                                            value="{{ old('end_date', $slcmpInchargeAssignment->end_date ? $slcmpInchargeAssignment->end_date->format('Y-m-d') : '') }}">
                                        @error('end_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status">Status <span class="text-danger">*</span></label>
                                        <select class="form-control @error('status') is-invalid @enderror"
                                            id="status" name="status" required>
                                            <option value="active" {{ old('status', $slcmpInchargeAssignment->status) == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ old('status', $slcmpInchargeAssignment->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                                <i class="fas fa-save"></i> Update Assignment
                            </button>
                            <a href="{{ route('slcmp-incharge-assignments.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <a href="{{ route('slcmp-incharge-assignments.show', $slcmpInchargeAssignment->id) }}" class="btn btn-info">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
$(document).ready(function() {
    // Toastr configuration
    toastr.options = {
        closeButton: true,
        progressBar: true,
        timeOut: 5000
    };

    // Show validation errors with Toastr
    @if($errors->any())
        @foreach($errors->all() as $error)
            toastr.error('{{ $error }}');
        @endforeach
    @endif

    // Bus Route Selection Change
    $('#bus_route_id').on('change', function() {
        const routeId = $(this).val();
        console.log('Route ID selected:', routeId);

        if (routeId) {
            // Show loading state
            $('#bus_no_display').html('<i class="fas fa-spinner fa-spin"></i> Loading bus details...');

            // Fetch bus details for selected route
            $.ajax({
                url: '{{ route("slcmp-incharge-assignments.get-bus-details") }}',
                type: 'GET',
                data: { bus_route_id: routeId },
                success: function(response) {
                    console.log('Bus details response:', response);

                    if (response.success && response.data) {
                        $('#bus_no_display').html(
                            '<strong>' + response.data.bus_no + '</strong><br>' +
                            '<small class="text-muted">' + response.data.bus_name + ' (' + response.data.bus_type + ')</small>'
                        );
                        toastr.success('Bus details loaded successfully!');
                    } else {
                        $('#bus_no_display').html('<span class="text-muted">Bus details not available</span>');
                        toastr.warning('Bus details not found for this route.');
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX Error:', xhr.responseText);
                    $('#bus_no_display').html('<span class="text-danger">Error loading bus details</span>');

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

    // Fetch SLCMP Details
    $('#fetch-slcmp-details').on('click', function() {
        const regimentNo = $('#slcmp_regiment_no').val().trim();

        if (!regimentNo) {
            toastr.warning('Please enter a regiment number first.');
            return;
        }

        const button = $(this);
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Searching...');

        // Don't clear existing data unless search is successful

        $.ajax({
            url: '{{ route("slcmp-incharge-assignments.get-slcmp-details") }}',
            type: 'GET',
            data: { regiment_no: regimentNo },
            success: function(response) {
                if (response.success) {
                    $('#slcmp_rank').val(response.data.rank);
                    $('#slcmp_name').val(response.data.name);

                    toastr.success('SLCMP details loaded successfully!');
                } else {
                    toastr.error(response.message || 'SLCMP not found.');
                }
            },
            error: function(xhr) {
                let message = 'Error loading SLCMP details.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                toastr.error(message);
            },
            complete: function() {
                button.prop('disabled', false).html('<i class="fas fa-search"></i> Search');
            }
        });
    });

    // Allow Enter key to trigger search
    $('#slcmp_regiment_no').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#fetch-slcmp-details').click();
        }
    });
});
</script>
@stop