@extends('adminlte::page')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-12">

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="row">
                    <!-- Establishment (Select2 Dropdown) -->
                    <div class="col-md-12">
                        <div class="form-group">
                            @php
                                $branchRoles = [
                                    'Bus Pass Subject Clerk (Branch)',
                                    'Staff Officer (Branch)',
                                    'Director (Branch)',
                                ];
                                $dmovRoles = [
                                    'System Administrator (DMOV)',
                                    'Subject Clerk (DMOV)',
                                    'Staff Officer 2 (DMOV)',
                                    'Staff Officer 1 (DMOV)',
                                    'Col Mov (DMOV)',
                                    'Director (DMOV)',
                                    'Bus Escort (DMOV)',
                                ];
                                $isBranchOnly =
                                    auth()->user()->hasAnyRole($branchRoles) && !auth()->user()->hasAnyRole($dmovRoles);
                            @endphp

                            @if ($isBranchOnly)
                                <!-- Hidden field for branch users (not DMOV) - auto-selected -->
                                <label for="establishment_display">Establishment</label>
                                <input type="text" class="form-control" id="establishment_display"
                                    value="{{ $establishments->first()->name ?? 'No Establishment Assigned' }}" readonly>
                                <input type="hidden" id="establishment_id" name="establishment_id"
                                    value="{{ $establishments->first()->id ?? '' }}">
                                <small class="form-text text-muted">
                                    Showing data for your assigned establishment
                                </small>
                            @else
                                <!-- Dropdown for DMOV users and other users -->
                                <label for="establishment_id">Establishment <span class="text-danger">*</span></label>
                                <select class="form-control @error('establishment_id') is-invalid @enderror"
                                    id="establishment_id" name="establishment_id" required>
                                    <option value="">Select Establishment</option>
                                    @foreach ($establishments as $establishment)
                                        <option value="{{ $establishment->id }}"
                                            {{ old('establishment_id') == $establishment->id ? 'selected' : '' }}>
                                            {{ $establishment->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('establishment_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">
                                    Select the establishment from the available options
                                </small>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card card-primary">
                        <div class="card-header">
                            <i class="nav-icon fas fa-qrcode nav-icon"></i>
                            {{ __('QR Download') }}
                            <small class="text">(Download QR codes for temporary bus pass cards)</small>
                        </div>

                        <div class="card-body">
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

@section('css')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap4.min.css') }}">
    <!-- DataTables Buttons CSS -->
    <link rel="stylesheet" href="{{ asset('css/buttons.bootstrap4.min.css') }}">
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap4.min.css') }}" rel="stylesheet" />
@stop

@section('js')
    <!-- DataTables Core JS -->
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap4.min.js') }}"></script>

    <!-- DataTables Buttons JS -->
    <script src="{{ asset('js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('js/buttons.print.min.js') }}"></script>

    <!-- Required for Excel export -->
    <script src="{{ asset('js/jszip.min.js') }}"></script>

    <!-- Required for PDF export -->
    <script src="{{ asset('js/pdfmake.min.js') }}"></script>
    <script src="{{ asset('js/vfs_fonts.js') }}"></script>

    <script src="{{ asset('js/select2.min.js') }}"></script>

    {{ $dataTable->scripts() }}

    <script>
        $(document).ready(function() {
            @php
                $branchRoles = ['Bus Pass Subject Clerk (Branch)', 'Staff Officer (Branch)', 'Director (Branch)'];
                $dmovRoles = ['System Administrator (DMOV)', 'Subject Clerk (DMOV)', 'Staff Officer 2 (DMOV)', 'Staff Officer 1 (DMOV)', 'Col Mov (DMOV)', 'Director (DMOV)', 'Bus Escort (DMOV)'];
                $isBranchOnly = auth()->user()->hasAnyRole($branchRoles) && !auth()->user()->hasAnyRole($dmovRoles);
            @endphp

            @if (!$isBranchOnly)
                // Initialize Select2 for establishment dropdown (DMOV users and other users)
                $('#establishment_id').select2({
                    theme: 'bootstrap4',
                    placeholder: 'Select Establishment',
                    allowClear: true,
                    width: '100%'
                });

                // Reload DataTable when establishment changes
                $('#establishment_id').on('change', function() {
                    window.LaravelDataTables['qr-download-table'].draw();
                });
            @else
                // For branch-only users, let DataTable load data via AJAX automatically
                // No need to call draw() since establishment_id is set
            @endif
        });
    </script>
@stop
