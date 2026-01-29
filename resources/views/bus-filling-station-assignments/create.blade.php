@extends('adminlte::page')

@section('title', 'Assign Bus Filling Station')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1><i class="fas fa-gas-pump"></i> Assign Bus Filling Station</h1>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-teal">
                    <div class="card-header">
                        <i class="nav-icon fas fa-gas-pump nav-icon"></i> {{ __('Assign Bus Filling Station') }}
                        <div class="card-tools">
                            <a href="{{ route('bus-filling-station-assignments.index') }}" class="btn btn-dark btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>

                    <form action="{{ route('bus-filling-station-assignments.store') }}" method="POST"
                        id="busFillingStationForm">
                        @csrf
                        <div class="card-body">

                            <div class="row">
                                <!-- Bus Name Selection (Dropdown) -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bus_id">Bus Name <span class="text-danger">*</span></label>
                                        <select class="form-control @error('bus_id') is-invalid @enderror" id="bus_id"
                                            name="bus_id" required>
                                            <option value="">Select Bus</option>
                                            @foreach ($buses as $bus)
                                                <option value="{{ $bus->id }}"
                                                    {{ old('bus_id') == $bus->id ? 'selected' : '' }}>
                                                    {{ $bus->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('bus_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Bus No (Auto Fill Label) -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Bus No</label>
                                        <div class="form-control" id="bus_no_display" style="background-color: #f8f9fa;">
                                            <span class="text-muted">Select a bus first</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Filling Station (Select2 Dropdown) -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="filling_station_id">Filling Station <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control @error('filling_station_id') is-invalid @enderror"
                                            id="filling_station_id" name="filling_station_id" required>
                                            <option value="">Select Filling Station</option>
                                            @foreach ($fillingStations as $station)
                                                <option value="{{ $station->id }}"
                                                    {{ old('filling_station_id') == $station->id ? 'selected' : '' }}>
                                                    {{ $station->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('filling_station_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Select the filling station from the available options
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
                                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active
                                            </option>
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
                                <i class="fas fa-save"></i> Assign Filling Station
                            </button>
                            <a href="{{ route('bus-filling-station-assignments.index') }}" class="btn btn-secondary">
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
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/select2-bootstrap4.min.css') }}" rel="stylesheet" />
    <style>
        /* Standard form control styling for consistency */
        #bus_id,
        #filling_station_id {
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

        #bus_id:focus,
        #filling_station_id:focus {
            color: #495057;
            background-color: #fff;
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        #bus_id:hover,
        #filling_station_id:hover {
            border-color: #adb5bd;
        }

        /* Select2 Bootstrap theme adjustments */
        .select2-container--bootstrap4 .select2-selection {
            height: calc(1.5em + 0.75rem + 2px) !important;
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2 for filling station dropdown
            $('#filling_station_id').select2({
                theme: 'bootstrap4',
                placeholder: 'Select Filling Station',
                allowClear: true,
                width: '100%'
            });

            // Toastr configuration
            toastr.options = {
                closeButton: true,
                progressBar: true,
                timeOut: 5000
            };

            // Show validation errors with Toastr
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    toastr.error('{{ $error }}');
                @endforeach
            @endif

            // Bus Selection Change
            $('#bus_id').on('change', function() {
                const busId = $(this).val();
                console.log('Bus ID selected:', busId);

                if (busId) {
                    // Show loading state
                    $('#bus_no_display').html(
                        '<i class="fas fa-spinner fa-spin"></i> Loading bus details...');

                    // Fetch bus details for selected bus
                    $.ajax({
                        url: '{{ route('bus-filling-station-assignments.get-bus-details') }}',
                        type: 'GET',
                        data: {
                            bus_id: busId
                        },
                        success: function(response) {
                            console.log('Bus details response:', response);

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
                                toastr.warning('Bus details not found for this bus.');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log('AJAX Error:', xhr.responseText);
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
                    $('#bus_no_display').html('<span class="text-muted">Select a bus first</span>');
                }
            });
        });
    </script>
@stop
