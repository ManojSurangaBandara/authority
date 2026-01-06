@extends('adminlte::page')

@section('title', 'Bus Pass Approvals')

@section('content_header')
    <h1>
        Bus Pass Approvals
        <small>
            Pending applications for your review
            @if (auth()->user()->isBranchUser() && auth()->user()->establishment)
                - {{ auth()->user()->establishment->name }}
            @endif
        </small>
    </h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clipboard-check"></i>
                        Pending Approvals
                    </h3>
                </div>
                <div class="card-body">
                    {{ $dataTable->table() }}
                </div>
            </div>
        </div>
    </div>

    @include('footer')

@stop

<!-- Modal container for dynamically loaded modals -->
<div id="modal-container"></div>

@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables-plugins/buttons/css/buttons.bootstrap4.min.css') }}">
    <link href="{{ asset('css/toastr.min.css') }}" rel="stylesheet">
    <style>
        .table th {
            white-space: nowrap;
        }

        .btn-group .btn {
            margin-right: 2px;
        }

        .bg-warning-light {
            background-color: #fff3cd !important;
            border-left: 4px solid #ffc107;
        }

        .table-warning {
            background-color: rgba(255, 193, 7, 0.1) !important;
        }

        .sltb-confirmation-alert {
            border-left: 4px solid #ffc107;
            background-color: #fff3cd;
        }

        .sltb-confirmation-alert .form-check-label {
            font-weight: 500;
            color: #856404;
        }

        .btn-success:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .sltb-confirmation-alert .form-check-input {
            transform: scale(1.2);
            margin-right: 8px;
        }

        .sltb-confirmation-alert .form-check-input:checked {
            background-color: #28a745;
            border-color: #28a745;
        }

        /* Enable horizontal scrolling for wide tables */
        .card-body {
            overflow-x: auto !important;
        }

        table.dataTable {
            width: 100% !important;
            max-width: 100% !important;

        }
    </style>
@stop

@push('js')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/buttons/js/buttons.colVis.min.js') }}"></script>
    {{ $dataTable->scripts() }}

    <script src="{{ asset('js/toastr.min.js') }}"></script>

    <script>
        // Toastr configuration
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        $(document).ready(function() {
            // Handle view button clicks - load modal via AJAX
            $(document).on('click', '.btn-info[data-toggle="modal"]', function(e) {
                e.preventDefault();

                var button = $(this);
                var appId = button.closest('tr').attr('id'); // Get application ID from row ID

                if (!appId || button.prop('disabled')) return;

                // Show loading state
                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

                // Load modal content via AJAX
                $.ajax({
                    url: '{{ url('bus-pass-approvals') }}/' + appId + '/modal',
                    method: 'GET',
                    success: function(response) {
                        // Clean up any existing modals and backdrops
                        $('.modal').modal('hide');
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open');

                        // Clear modal container
                        $('#modal-container').empty();

                        // Add new modals
                        $('#modal-container').html(response.modal + response.actionModals);

                        // Show the view modal
                        $('#viewModal' + appId).modal('show');

                        // Re-enable button
                        button.prop('disabled', false).html('<i class="fas fa-eye"></i>');
                    },
                    error: function(xhr) {
                        console.error('Error loading modal:', xhr);
                        alert('Error loading application details. Please try again.');

                        // Re-enable button
                        button.prop('disabled', false).html('<i class="fas fa-eye"></i>');
                    }
                });
            });

            // Handle modal close events to clean up properly
            $(document).on('hidden.bs.modal', '.modal', function() {
                // Clean up modal backdrops and body classes
                if ($('.modal:visible').length === 0) {
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open');
                }

                // Remove the modal from DOM after it's hidden
                $(this).remove();
            });

            // Handle modal show events
            $(document).on('show.bs.modal', '.modal', function() {
                // Ensure body has modal-open class
                $('body').addClass('modal-open');
            });
        });
    </script>
@endpush
