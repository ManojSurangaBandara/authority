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
                            @if(auth()->user()->hasAnyRole(['Bus Pass Subject Clerk (Branch)', 'Staff Officer (Branch)', 'Director (Branch)']))
                                <!-- Hidden field for branch users - auto-selected -->
                                <label for="establishment_display">Establishment</label>
                                <input type="text" class="form-control" id="establishment_display" 
                                       value="{{ $establishments->first()->name ?? 'No Establishment Assigned' }}" readonly>
                                <input type="hidden" id="establishment_id" name="establishment_id" value="{{ $establishments->first()->id ?? '' }}">
                                <small class="form-text text-muted">
                                    Showing data for your assigned establishment
                                </small>
                            @else
                                <!-- Dropdown for other users -->
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
                    <div class="card card-teal">
                        <div class="card-header"><i class="nav-icon fas fa-id-card nav-icon"></i> {{ __('Bus Pass Applications') }}
                            {{-- <a href="{{ route('bus-pass-applications.create') }}" class="btn btn-sm btn-primary float-right">Add New
                                Application</a> --}}
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
@endsection

@push('js')
    <!-- DataTables Core JS -->
    <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    
    <!-- DataTables Buttons JS -->
    <script src="//cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="//cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
    <script src="//cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="//cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    
    <!-- Required for Excel export -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    
    <!-- Required for PDF export -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    
    {{ $dataTable->scripts() }}
@endpush

@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <!-- DataTables Buttons CSS -->
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">
    
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

        #bus_id:focus, #establishment_id:focus {
            color: #495057;
            background-color: #fff;
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        #bus_id:hover, #establishment_id:hover {
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

        .card-header .dt-buttons {
            display: inline-block;
        }

        /* Ensure buttons are properly spaced */
        .dt-button {
            margin-right: 5px !important;
        }
    </style>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    @if(!auth()->user()->hasAnyRole(['Bus Pass Subject Clerk (Branch)', 'Staff Officer (Branch)', 'Director (Branch)']))
        // Initialize Select2 for establishment dropdown (non-branch users)
        $('#establishment_id').select2({
            theme: 'bootstrap4',
            placeholder: 'Select Establishment',
            allowClear: true,
            width: '100%'
        });

        // Reload DataTable when establishment changes
        $('#establishment_id').on('change', function() {
            window.LaravelDataTables['bus-pass-application-table'].draw();
        });
    @else
        // Auto-load data for branch users
        setTimeout(function() {
            if (window.LaravelDataTables && window.LaravelDataTables['bus-pass-application-table']) {
                window.LaravelDataTables['bus-pass-application-table'].draw();
            }
        }, 1000);
    @endif

    // Add export buttons to DataTable after it's initialized
    setTimeout(function() {
        if (window.LaravelDataTables && window.LaravelDataTables['bus-pass-application-table']) {
            var table = window.LaravelDataTables['bus-pass-application-table'];
            
            // Add export buttons
            new $.fn.dataTable.Buttons(table, {
                dom: {
                    button: {
                        className: 'btn btn-sm'
                    }
                },
                buttons: [
                    {
                        extend: 'copy',
                        text: '<i class="fas fa-copy"></i> Copy',
                        className: 'btn-info',
                        exportOptions: {
                            columns: ':not(.no-export)'
                        }
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        className: 'btn-success',
                        exportOptions: {
                            columns: ':not(.no-export)'
                        }
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn-success',
                        exportOptions: {
                            columns: ':not(.no-export)'
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'btn-danger',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: {
                            columns: ':not(.no-export)'
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Print',
                        className: 'btn-secondary',
                        exportOptions: {
                            columns: ':not(.no-export)'
                        }
                    }
                ]
            });

            // Append buttons to card header
            table.buttons().container().appendTo($('.card-header'));
        }
    }, 2000);

});
</script>
@stop
