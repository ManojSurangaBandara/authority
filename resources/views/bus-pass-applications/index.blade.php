@extends('adminlte::page')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-12">

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <div class="card mt-3">
                    <div class="card card-teal">
                        <div class="card-header"><i class="nav-icon fas fa-id-card nav-icon"></i>
                            {{ __('Bus Pass Applications') }}
                            @if (auth()->user()->hasRole('Bus Pass Subject Clerk (Branch)'))
                                <div class="float-right">
                                    <a href="{{ route('bus-pass-applications.create') }}"
                                        class="btn btn-sm btn-primary mr-2">
                                        <i class="fas fa-plus"></i> Create Army Application
                                    </a>
                                    <a href="{{ route('bus-pass-applications.create-navy') }}"
                                        class="btn btn-sm btn-info mr-2">
                                        <i class="fas fa-anchor"></i> Create Navy Application
                                    </a>
                                    <a href="{{ route('bus-pass-applications.create-airforce') }}"
                                        class="btn btn-sm btn-warning mr-2">
                                        <i class="fas fa-plane"></i> Create Airforce Application
                                    </a>
                                    <a href="{{ route('bus-pass-applications.create-civil') }}"
                                        class="btn btn-sm btn-success">
                                        <i class="fas fa-user-plus"></i> Create Civil Application
                                    </a>
                                </div>
                            @endif
                        </div>

                        <div class="card-body">
                            @if (auth()->user()->isMovementUser())
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="establishment_filter">Filter by Establishment:</label>
                                        <select id="establishment_filter" class="form-control form-control-sm">
                                            <option value="">All Establishments</option>
                                            @foreach (\App\Models\Establishment::where('is_active', true)->orderBy('name')->get() as $establishment)
                                                <option value="{{ $establishment->id }}"
                                                    {{ request('establishment_filter') == $establishment->id ? 'selected' : '' }}>
                                                    {{ $establishment->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="status_filter">Filter by Status:</label>
                                        <select id="status_filter" class="form-control form-control-sm">
                                            <option value="">All Statuses</option>
                                            @php
                                                $statuses = [
                                                    'pending_subject_clerk' => 'Pending - Subject Clerk Review',
                                                    'pending_staff_officer_branch' =>
                                                        'Pending - Staff Officer (Branch/Dte)',
                                                    'forwarded_to_movement' => 'Forwarded to Movement',
                                                    'pending_staff_officer_2_mov' =>
                                                        'Pending - Staff Officer 2 (Movement)',
                                                    'pending_col_mov' => 'Pending - Colonel Movement',
                                                    'approved_for_integration' =>
                                                        'Approved for Branch Card Integration',
                                                    'approved_for_temp_card' => 'Approved for Temporary Card',
                                                    'integrated_to_branch_card' => 'Integrated to Branch Card',
                                                    'temp_card_printed' => 'Temporary Card Printed',
                                                    'temp_card_handed_over' => 'Temporary Card Handed Over',
                                                    'rejected' => 'Rejected',
                                                    'deactivated' => 'Deactivated',
                                                ];
                                            @endphp
                                            @foreach ($statuses as $key => $label)
                                                <option value="{{ $key }}"
                                                    {{ request('status_filter') == $key ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button type="button" id="apply_filters" class="btn btn-primary btn-sm mr-2">
                                            <i class="fas fa-filter"></i> Apply Filters
                                        </button>
                                        <button type="button" id="clear_filters" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-times"></i> Clear Filters
                                        </button>
                                    </div>
                                </div>
                            @endif
                            <div class="table-responsive">
                                {{ $dataTable->table() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('footer')

@endsection

@push('js')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/buttons/js/buttons.colVis.min.js') }}"></script>
    {{ $dataTable->scripts() }}

    @if (auth()->user()->isMovementUser())
        <script>
            $(document).ready(function() {
                // Apply filters button click
                $('#apply_filters').on('click', function() {
                    var establishmentId = $('#establishment_filter').val();
                    var statusValue = $('#status_filter').val();

                    // Update URL with filter parameters
                    var url = new URL(window.location);
                    if (establishmentId) {
                        url.searchParams.set('establishment_filter', establishmentId);
                    } else {
                        url.searchParams.delete('establishment_filter');
                    }
                    if (statusValue) {
                        url.searchParams.set('status_filter', statusValue);
                    } else {
                        url.searchParams.delete('status_filter');
                    }

                    // Reload the page with new filters
                    window.location.href = url.toString();
                });

                // Clear filters button click
                $('#clear_filters').on('click', function() {
                    var url = new URL(window.location);
                    url.searchParams.delete('establishment_filter');
                    url.searchParams.delete('status_filter');
                    window.location.href = url.toString();
                });
            });
        </script>
    @endif
@endpush
