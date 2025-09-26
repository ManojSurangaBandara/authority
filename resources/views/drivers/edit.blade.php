@extends('adminlte::page')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-7">

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif
                <div class="card mt-3">
                    <div class="card card-teal">
                        <div class="card-header"><i class="nav-icon fas fa-user nav-icon"></i> {{ __('Edit Driver') }}</div>
                        <div class="card-body">
                            @if ($isUsed ?? false)
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Note:</strong> This driver has {{ $activeAssignmentsCount }} active
                                    assignment(s).
                                    The regiment number cannot be changed while the driver is assigned.
                                </div>
                            @endif

                            <form action="{{ route('drivers.update', $driver->id) }}" method="POST" id="driverForm">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label for="regiment_no">Regiment Number:</label>
                                    <div class="input-group">
                                        <input type="text" name="regiment_no" id="regiment_no" required
                                            class="form-control" value="{{ $driver->regiment_no }}"
                                            {{ $isUsed ?? false ? 'readonly' : '' }}>
                                        @if (!($isUsed ?? false))
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-info" id="fetchDriverBtn">Fetch
                                                    Details</button>
                                            </div>
                                        @endif
                                    </div>
                                    @if ($isUsed ?? false)
                                        <small class="form-text text-muted">
                                            <i class="fas fa-lock"></i> Regiment number is locked because this driver has
                                            active assignments.
                                        </small>
                                    @endif
                                    @error('regiment_no')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="rank">Rank:</label>
                                    <input type="text" name="rank" id="rank" required class="form-control"
                                        value="{{ $driver->rank }}">
                                    @error('rank')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="name">Name:</label>
                                    <input type="text" name="name" id="name" required class="form-control"
                                        value="{{ $driver->name }}">
                                    @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="contact_no">Contact Number:</label>
                                    <input type="text" name="contact_no" id="contact_no" required class="form-control"
                                        value="{{ $driver->contact_no }}">
                                    @error('contact_no')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                    <a href="{{ route('drivers.index') }}" class="btn btn-sm btn-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('footer')
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $('#fetchDriverBtn').on('click', function() {
                const regimentNo = $('#regiment_no').val();
                if (!regimentNo) {
                    alert('Please enter a Regiment Number');
                    return;
                }

                // Show loading indicator
                $(this).html('<i class="fas fa-spinner fa-spin"></i> Fetching...');
                $(this).attr('disabled', true);

                // Make AJAX call to get driver details
                $.ajax({
                    url: "{{ route('drivers.get-details') }}",
                    type: "GET",
                    data: {
                        regiment_no: regimentNo
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            // Fill form fields with returned data
                            $('#rank').val(response.data.rank);
                            $('#name').val(response.data.name);
                            // Keep the contact_no field as is, since it's not in the API response
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Error fetching driver details. Please try again.');
                        console.error(xhr.responseText);
                    },
                    complete: function() {
                        // Reset button state
                        $('#fetchDriverBtn').html('Fetch Details');
                        $('#fetchDriverBtn').attr('disabled', false);
                    }
                });
            });
        });
    </script>
@endpush
