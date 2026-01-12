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
                        <div class="card-header">
                            <i class="fas fa-fw fa-landmark"></i> GS Division
                            <a href="{{ route('gs-devision.create') }}" class="btn btn-sm btn-primary float-right">Add New</a>
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
@endpush
