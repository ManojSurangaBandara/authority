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
                        <div class="card-header"><i class="nav-icon fas fa-user nav-icon"></i> {{ __('Add New Driver') }}
                        </div>
                        <div class="card-body">
                            <form action="{{ route('drivers.store') }}" method="POST" id="driverForm">
                                @csrf
                                <div class="mb-3">
                                    <label for="regiment_no">Regiment Number:</label>
                                    <div class="input-group">
                                        <input type="text" name="regiment_no" id="regiment_no" required
                                            class="form-control" value="{{ old('regiment_no') }}">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-info" id="fetchDriverBtn">Fetch
                                                Details</button>
                                        </div>
                                    </div>
                                    @error('regiment_no')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="rank">Rank:</label>
                                    <input type="text" name="rank" id="rank" required class="form-control"
                                        value="{{ old('rank') }}" readonly>
                                    @error('rank')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="name">Name:</label>
                                    <input type="text" name="name" id="name" required class="form-control"
                                        value="{{ old('name') }}" readonly>
                                    @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="contact_no">Contact Number:</label>
                                    <input type="tel" name="contact_no" pattern="[0-9]{10}" maxlength="10"  inputmode="numeric" id="contact_no" required oninvalid="this.setCustomValidity('Please enter a valid 10-digit mobile number')" oninput="this.setCustomValidity('')" class="form-control"
                                        value="{{ old('contact_no') }}">
                                    @error('contact_no')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <button type="submit" class="btn btn-sm btn-primary">Save</button>
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

                            // Enable form submission
                            $('#rank').prop('readonly', true);
                            $('#name').prop('readonly', true);
                            $('#contact_no').prop('readonly', false);
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
