@extends('adminlte::page')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-12">

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="row">
                    <!-- Establishment (Select2 Dropdown) -->
                    <div class="col-md-12">
                        <div class="form-group">
                            @if (auth()->user()->hasAnyRole(['Bus Pass Subject Clerk (Branch)', 'Staff Officer (Branch)', 'Director (Branch)']))
                                <!-- Hidden field for branch users - auto-selected -->
                                <label for="establishment_display">Establishment</label>
                                <input type="text" class="form-control" id="establishment_display"
                                    value="{{ $establishments->first()->name ?? 'No Establishment Assigned' }}" readonly>
                                <input type="hidden" id="establishment_id" name="establishment_id"
                                    value="{{ $establishments->first()->id ?? '' }}">
                                <small class="form-text text-muted">
                                    Showing data for your assigned establishment
                                </small>
                            @else
                                <!-- Dropdown for other users -->
                                <label for="establishment_id">Establishment</label>
                                <select class="form-control @error('establishment_id') is-invalid @enderror"
                                    id="establishment_id" name="establishment_id">
                                    <option value="">All Establishments</option>
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
                    <div class="card card-teal">
                        <div class="card-header"><i class="nav-icon fas fa-ban nav-icon"></i>
                            {{ __('Deactivated Bus Pass Applications') }}
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

<!-- Reactivate Modal -->
<div class="modal fade" id="reactivateModal" tabindex="-1" role="dialog" aria-labelledby="reactivateModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reactivateModalLabel">Reactivate Bus Pass Application</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="reactivateForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to reactivate this bus pass application?</p>
                    <div class="form-group">
                        <label for="reactivate_remarks">Remarks <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reactivate_remarks" name="remarks" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Reactivate</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
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

    {{ $dataTable->scripts() }}

    <script>
        // Handle reactivate modal
        $('#reactivateModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var applicationId = button.data('application-id');
            var modal = $(this);
            var form = modal.find('#reactivateForm');
            form.attr('action', '/bus-pass-approvals/' + applicationId + '/reactivate');
        });

        // Reload DataTable when establishment changes
        $('#establishment_id').on('change', function() {
            window.LaravelDataTables['deactivated-bus-pass-application-table'].draw();
        });
    </script>
@endpush

@section('css')
    <link href="{{ asset('css/toastr.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/select2-bootstrap4.min.css') }}" rel="stylesheet" />

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap4.min.css') }}">
    <!-- DataTables Buttons CSS -->
    <link rel="stylesheet" href="{{ asset('css/buttons.bootstrap4.min.css') }}">

    <style>
        /* Standard form control styling for consistency */
        #establishment_id {
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
        #establishment_id:focus {
            color: #495057;
            background-color: #fff;
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        #bus_id:hover,
        #establishment_id:hover {
            border-color: #adb5bd;
        }

        /* Select2 Bootstrap theme adjustments */
        .select2-container--bootstrap4 .select2-selection {
            height: calc(1.5em + 0.75rem + 2px) !important;
        }

        /* DataTables Export Buttons Styling */
        .dt-buttons {
            float: right;
            margin-left: 10px;
        }

        .dt-buttons .btn {
            margin-left: 5px;
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
        }

        /* Modal styling */
        .modal-content {
            border-radius: 0.375rem;
        }

        .modal-header {
            border-bottom: 1px solid #dee2e6;
            border-top-left-radius: 0.375rem;
            border-top-right-radius: 0.375rem;
        }

        .modal-footer {
            border-top: 1px solid #dee2e6;
            border-bottom-left-radius: 0.375rem;
            border-bottom-right-radius: 0.375rem;
        }
    </style>
@stop
