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
                        <div class="card-header"><i class="nav-icon fas fa-user nav-icon"></i> {{ __('Edit Escort') }}</div>
                        <div class="card-body">
                            @if ($isUsed ?? false)
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Note:</strong> This escort has {{ $activeAssignmentsCount }} active
                                    assignment(s).
                                    The regiment number cannot be changed while the escort is assigned.
                                </div>
                            @endif

                            <form action="{{ route('escorts.update', $escort->id) }}" method="POST" id="escortForm">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label for="regiment_no">Regiment Number:</label>
                                    <div class="input-group">
                                        <input type="text" name="regiment_no" id="regiment_no" required
                                            class="form-control" value="{{ $escort->regiment_no }}"
                                            {{ $isUsed ?? false ? 'readonly' : '' }}>
                                        @if (!($isUsed ?? false))
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-info" id="fetchEscortBtn">Fetch
                                                    Details</button>
                                            </div>
                                        @endif
                                    </div>
                                    @if ($isUsed ?? false)
                                        <small class="form-text text-muted">
                                            <i class="fas fa-lock"></i> Regiment number is locked because this escort has
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
                                        value="{{ $escort->rank }}">
                                    @error('rank')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="name">Name:</label>
                                    <input type="text" name="name" id="name" required class="form-control"
                                        value="{{ $escort->name }}">
                                    @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="eno">E Number:</label>
                                    <input type="text" name="eno" id="eno" required class="form-control"
                                        value="{{ $escort->eno }}" {{ $isUsed ?? false ? 'readonly' : '' }}>
                                    @if ($isUsed ?? false)
                                        <small class="form-text text-muted">
                                            <i class="fas fa-lock"></i> E Number is locked because this escort has
                                            active assignments.
                                        </small>
                                    @endif
                                    @error('eno')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="contact_no">Contact Number:</label>
                                    <input type="tel" name="contact_no" id="contact_no" required pattern="[0-9]{10}" maxlength="10" inputmode="numeric" oninvalid="this.setCustomValidity('Please enter a valid 10-digit mobile number')" oninput="this.setCustomValidity('')" class="form-control"
                                        value="{{ $escort->contact_no }}">
                                    @error('contact_no')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                    <a href="{{ route('escorts.index') }}" class="btn btn-sm btn-secondary">Cancel</a>
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
            $('#fetchEscortBtn').on('click', function() {
                const regimentNo = $('#regiment_no').val();
                if (!regimentNo) {
                    alert('Please enter a Regiment Number');
                    return;
                }

                // Show loading indicator
                $(this).html('<i class="fas fa-spinner fa-spin"></i> Fetching...');
                $(this).attr('disabled', true);

                // Make AJAX call to get escort details
                $.ajax({
                    url: "{{ route('escorts.get-details') }}",
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
                        alert('Error fetching escort details. Please try again.');
                        console.error(xhr.responseText);
                    },
                    complete: function() {
                        // Reset button state
                        $('#fetchEscortBtn').html('Fetch Details');
                        $('#fetchEscortBtn').attr('disabled', false);
                    }
                });
            });
        });
    </script>
@endpush
