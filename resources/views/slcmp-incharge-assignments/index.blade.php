@extends('adminlte::page')

@section('title', 'SLCMP In-charge Assignments')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1><i class="fas fa-shield-alt"></i> SLCMP In-charge Assignments</h1>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-teal">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-shield-alt"></i> SLCMP In-charge Assignments
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('slcmp-incharge-assignments.create') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-plus"></i> Assign SLCMP In-charge
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    {{ $dataTable->table(['class' => 'table table-bordered table-striped']) }}
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap4.min.css">
    <style>
        /* Hide all DataTable processing/loading animations */
        .dataTables_processing {
            display: none !important;
        }

        .dataTables_processing div {
            display: none !important;
        }

        .dt-processing {
            display: none !important;
        }

        /* Hide loading spinners */
        .dataTables_wrapper .dataTables_processing {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%;
            height: 40px;
            margin-left: -50%;
            margin-top: -25px;
            padding-top: 20px;
            text-align: center;
            font-size: 1.2em;
            background-color: white;
            background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(rgba(255, 255, 255, 0)), to(rgba(255, 255, 255, 0.9)));
            background: -webkit-linear-gradient(top, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.9) 100%);
            background: -moz-linear-gradient(top, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.9) 100%);
            background: -ms-linear-gradient(top, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.9) 100%);
            background: -o-linear-gradient(top, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.9) 100%);
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.9) 100%);
            display: none !important;
        }

        /* Custom styling for table */
        #slcmpinchargeassignment-table {
            width: 100% !important;
        }

        /* Ensure no loading states show */
        .dataTable tbody tr.odd,
        .dataTable tbody tr.even {
            background-color: transparent;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>

    <script>
        // Disable all DataTable animations and processing indicators
        $.fn.dataTable.ext.errMode = 'none';

        // Override processing display
        $.fn.dataTable.ext.feature.push({
            "fnInit": function(oSettings) {
                return null;
            },
            "cFeature": "P",
            "sFeature": "Processing"
        });

        $(document).ready(function() {
            // Hide any processing elements that might appear
            $('body').on('DOMNodeInserted', function(e) {
                if ($(e.target).hasClass('dataTables_processing')) {
                    $(e.target).hide();
                }
            });

            // Ensure no processing indicators show
            $('.dataTables_processing').hide();

            console.log('DataTable animation removal scripts loaded');
        });

        // Override the processing function to do nothing
        $.fn.dataTable.Api.register('processing()', function(show) {
            return this;
        });
    </script>

    {{ $dataTable->scripts() }}
@stop
