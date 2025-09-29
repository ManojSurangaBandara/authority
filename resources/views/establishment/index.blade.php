@extends('adminlte::page')

@section('plugins.Datatables', true)

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-12">

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <div class="card mt-3">
                    <div class="card card-teal">
                        <div class="card-header"><i class="nav-icon fas fa-building"></i> {{ __('Establishment') }}
                            <a href="{{ route('establishment.create') }}" class="btn btn-sm btn-primary float-right">Add New
                                Establishment</a>
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
    {{ $dataTable->scripts() }}
    <script>
        $(document).ready(function() {
            // Initialize tooltips after DataTable is loaded
            $('#establishment-table').on('draw.dt', function() {
                $('[data-toggle="tooltip"]').tooltip();
            });

            // Initialize tooltips on page load
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
