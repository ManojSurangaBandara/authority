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
                    <div class="col-md-6">
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
                        <div class="card-header"><i class="nav-icon fas fa-route nav-icon"></i>
                            {{ __('Trips') }}
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
            // Function to reload table with filters
            function reloadTable() {
                var table = $('#trips-table').DataTable();
                var url = '{{ route('trips.index') }}';

                // Get filter values
                var date = $('#date').val();
                var tripType = $('#trip_type').val();
                var route = $('#route').val();

                // Build query string
                var params = [];
                if (date) params.push('date=' + encodeURIComponent(date));
                if (tripType) params.push('trip_type=' + encodeURIComponent(tripType));
                if (route) params.push('route=' + encodeURIComponent(route));

                if (params.length > 0) {
                    url += '?' + params.join('&');
                }

                table.ajax.url(url).load();
            }

            // Attach change events to filters
            $('#date, #trip_type, #route').on('change', function() {
                reloadTable();
            });
        });
    </script>
@endpush
