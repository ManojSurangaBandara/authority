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
                        <div class="card-header"><i class="nav-icon fas fa-user-shield nav-icon"></i>
                            {{ __('SLCMP In Charges') }}
                            <a href="{{ route('slcmp-incharges.create') }}" class="btn btn-sm btn-primary float-right">Add New
                                SLCMP In Charge</a>
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

@section('plugins.Datatables', true)

@push('js')
    {{ $dataTable->scripts() }}
    <script>
        $(document).ready(function() {
            // Initialize tooltips after DataTable is loaded
            $('#slcmp-incharge-table').on('draw.dt', function() {
                $('[data-toggle="tooltip"]').tooltip();
            });

            // Initialize tooltips on page load
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
