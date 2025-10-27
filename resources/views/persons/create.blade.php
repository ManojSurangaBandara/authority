@extends('adminlte::page')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif
                <div class="card mt-3">
                    <div class="card card-teal">
                        <div class="card-header"><i class="nav-icon fas fa-user nav-icon"></i> {{ __('Add New Person') }}
                        </div>
                        <div class="card-body">
                            <form action="{{ route('persons.store') }}" method="POST" id="personForm">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="regiment_no">Regiment Number:</label>
                                            <div class="input-group">
                                                <input type="text" name="regiment_no" id="regiment_no" required
                                                    class="form-control" value="{{ old('regiment_no') }}">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-info" id="fetchPersonBtn">Fetch
                                                        Details</button>
                                                </div>
                                            </div>
                                            @error('regiment_no')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="rank">Rank:</label>
                                            <input type="text" name="rank" id="rank" required
                                                class="form-control" value="{{ old('rank') }}">
                                            @error('rank')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name">Name:</label>
                                            <input type="text" name="name" id="name" required
                                                class="form-control" value="{{ old('name') }}">
                                            @error('name')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="unit">Unit:</label>
                                            <input type="text" name="unit" id="unit" required
                                                class="form-control" value="{{ old('unit') }}">
                                            @error('unit')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nic">NIC:</label>
                                            <input type="text" name="nic" id="nic" required
                                                class="form-control" value="{{ old('nic') }}">
                                            @error('nic')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="army_id">Army ID:</label>
                                            <input type="text" name="army_id" id="army_id" required
                                                class="form-control" value="{{ old('army_id') }}">
                                            @error('army_id')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="permanent_address">Permanent Address:</label>
                                    <textarea name="permanent_address" id="permanent_address" required class="form-control" rows="3">{{ old('permanent_address') }}</textarea>
                                    <small class="form-text text-muted">This field can be edited manually</small>
                                    @error('permanent_address')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="telephone_no">Telephone No:</label>
                                            <input type="text" name="telephone_no" id="telephone_no" required
                                                class="form-control" value="{{ old('telephone_no') }}">
                                            <small class="form-text text-muted">This field can be edited manually</small>
                                            @error('telephone_no')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="grama_seva_division">Grama Seva Division:</label>
                                            <input type="text" name="grama_seva_division" id="grama_seva_division"
                                                required class="form-control" value="{{ old('grama_seva_division') }}">
                                            <small class="form-text text-muted">This field can be edited manually</small>
                                            @error('grama_seva_division')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="nearest_police_station">Nearest Police Station:</label>
                                            <input type="text" name="nearest_police_station"
                                                id="nearest_police_station" required class="form-control"
                                                value="{{ old('nearest_police_station') }}">
                                            <small class="form-text text-muted">This field can be edited manually</small>
                                            @error('nearest_police_station')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                    <a href="{{ route('persons.index') }}" class="btn btn-sm btn-secondary">Cancel</a>
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
            $('#fetchPersonBtn').on('click', function() {
                const regimentNo = $('#regiment_no').val();
                if (!regimentNo) {
                    alert('Please enter a Regiment Number');
                    return;
                }

                // Show loading indicator
                $(this).html('<i class="fas fa-spinner fa-spin"></i> Fetching...');
                $(this).attr('disabled', true);

                // Make AJAX call to get person details
                $.ajax({
                    url: "{{ route('persons.get-details') }}",
                    type: "GET",
                    data: {
                        regiment_no: regimentNo
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            // Fill form fields with returned data
                            $('#rank').val(response.data.rank || '');
                            $('#name').val(response.data.name || '');
                            $('#unit').val(response.data.unit || '');
                            $('#nic').val(response.data.nic || '');
                            $('#army_id').val(response.data.army_id || '');
                            $('#permanent_address').val(response.data.permanent_address || '');
                            $('#telephone_no').val(response.data.telephone_no || '');
                            $('#grama_seva_division').val(response.data.grama_seva_division ||
                                '');
                            $('#nearest_police_station').val(response.data
                                .nearest_police_station || '');

                            // Set readonly based on whether API returned data
                            $('#rank').prop('readonly', response.data.rank && response.data.rank
                                .trim() !== '');
                            $('#name').prop('readonly', response.data.name && response.data.name
                                .trim() !== '');
                            $('#unit').prop('readonly', response.data.unit && response.data.unit
                                .trim() !== '');
                            $('#nic').prop('readonly', response.data.nic && response.data.nic
                                .trim() !== '');
                            $('#army_id').prop('readonly', response.data.army_id && response
                                .data.army_id.trim() !== '');

                            // These fields always remain editable for manual input
                            $('#permanent_address').prop('readonly', false);
                            $('#telephone_no').prop('readonly', false);
                            $('#grama_seva_division').prop('readonly', false);
                            $('#nearest_police_station').prop('readonly', false);

                            // Update field styling to indicate which are editable
                            $('input[readonly], textarea[readonly]').addClass('bg-light');
                            $('input:not([readonly]), textarea:not([readonly])').removeClass(
                                'bg-light');
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Error fetching person details. Please try again.');
                        console.error(xhr.responseText);
                    },
                    complete: function() {
                        // Reset button state
                        $('#fetchPersonBtn').html('Fetch Details');
                        $('#fetchPersonBtn').attr('disabled', false);
                    }
                });
            });
        });
    </script>
@endpush
