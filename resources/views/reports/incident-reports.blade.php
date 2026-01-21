@extends('adminlte::page')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-12">

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="row">
                    <!-- Date Filter -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date">Date</label>
                            <input type="date" class="form-control" id="date" name="date">
                        </div>
                    </div>

                    <!-- Incident Type Filter -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="type">Incident Type</label>
                            <select class="form-control" id="type" name="type">
                                <option value="">All Types</option>
                                @foreach ($types as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Trip Type Filter -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="trip_type">Trip Type</label>
                            <select class="form-control" id="trip_type" name="trip_type">
                                <option value="">All Trips</option>
                                @foreach ($tripTypes as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Route Filter -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="route">Route</label>
                            <select class="form-control" id="route" name="route">
                                <option value="">All Routes</option>
                                @foreach ($routes as $route)
                                    <option value="{{ $route['id'] }}">{{ $route['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card card-teal">
                        <div class="card-header"><i class="nav-icon fas fa-exclamation-triangle nav-icon"></i>
                            {{ __('Incident Reports') }}
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
        $(document).ready(function() {
            var table = $('#incident-reports-table').DataTable();

            $('#date, #type, #trip_type, #route').on('change', function() {
                var params = {
                    date: $('#date').val(),
                    type: $('#type').val(),
                    trip_type: $('#trip_type').val(),
                    route: $('#route').val()
                };

                // Remove empty parameters
                Object.keys(params).forEach(key => {
                    if (!params[key]) {
                        delete params[key];
                    }
                });

                var queryString = $.param(params);
                var url = '{{ route('incident-reports.index') }}';
                if (queryString) {
                    url += '?' + queryString;
                }

                table.ajax.url(url).load();
            });

            $('#incident-reports-table tbody').on('click', 'tr', function() {
                var href = $(this).data('href');
                if (href) {
                    window.location.href = href;
                }
            });

            $('#incident-reports-table tbody').on('click', 'a', function(e) {
                e.stopPropagation();
            });
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
        #incident-reports-table tbody tr {
            cursor: pointer;
        }
    </style>
@endsection
