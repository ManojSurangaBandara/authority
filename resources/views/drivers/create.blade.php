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
                                    <label for="driver_type">Driver Type:</label>
                                    <select name="driver_type" id="driver_type" class="form-control" required>
                                        <option value="Army" {{ old('driver_type', 'Army') == 'Army' ? 'selected' : '' }}>
                                            Army</option>
                                        <option value="Civil" {{ old('driver_type') == 'Civil' ? 'selected' : '' }}>Civil
                                        </option>
                                    </select>
                                    @error('driver_type')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- army-specific fields --}}
                                <div id="army-fields">
                                    <div class="mb-3">
                                        <label for="regiment_no">Regiment Number:</label>
                                        <div class="input-group">
                                            <input type="text" name="regiment_no" id="regiment_no" class="form-control"
                                                value="{{ old('regiment_no') }}">
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
                                        <input type="text" name="rank" id="rank" class="form-control"
                                            value="{{ old('rank') }}" readonly>
                                        @error('rank')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- civil-specific fields --}}
                                <div id="civil-fields" style="display: none;">
                                    <div class="mb-3">
                                        <label for="nic">NIC:</label>
                                        <input type="text" name="nic" id="nic" class="form-control"
                                            value="{{ old('nic') }}" maxlength="12" pattern="[0-9].*"
                                            title="Maximum 12 characters; must start with a digit">
                                        <small class="form-text text-muted">NIC must be at most 12 characters and start with
                                            a number.</small>
                                        @error('nic')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
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
                                    <input type="tel" name="contact_no" pattern="[0-9]{10}" maxlength="10"
                                        inputmode="numeric" id="contact_no" required
                                        oninvalid="this.setCustomValidity('Please enter a valid 10-digit mobile number')"
                                        oninput="this.setCustomValidity('')" class="form-control"
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
            function toggleFields() {
                const type = $('#driver_type').val();
                if (type === 'Army') {
                    $('#army-fields').show();
                    $('#civil-fields').hide();
                    // reset civil inputs
                    $('#nic').val('');
                    // name and rank readonly until fetched
                    $('#name').prop('readonly', true);
                    $('#rank').prop('readonly', true);
                    $('#fetchDriverBtn').show();
                    // required attributes
                    $('#regiment_no').prop('required', true);
                    $('#rank').prop('required', true);
                    $('#nic').prop('required', false);
                } else {
                    $('#army-fields').hide();
                    $('#civil-fields').show();
                    // clear army inputs
                    $('#regiment_no').val('');
                    $('#rank').val('');
                    $('#name').prop('readonly', false);
                    $('#fetchDriverBtn').hide();
                    $('#regiment_no').prop('required', false);
                    $('#rank').prop('required', false);
                    $('#nic').prop('required', true);
                }
            }

            // initialize on page load
            toggleFields();
            $('#driver_type').on('change', toggleFields);

            $('#fetchDriverBtn').on('click', function() {
                const regimentNo = $('#regiment_no').val();
                if (!regimentNo) {
                    alert('Please enter a Regiment Number');
                    return;
                }
                // add driver_type to request for server check

                // Show loading indicator
                $(this).html('<i class="fas fa-spinner fa-spin"></i> Fetching...');
                $(this).attr('disabled', true);

                // Make AJAX call to get driver details
                $.ajax({
                    url: "{{ route('drivers.get-details') }}",
                    type: "GET",
                    data: {
                        regiment_no: regimentNo,
                        driver_type: $('#driver_type').val()
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
