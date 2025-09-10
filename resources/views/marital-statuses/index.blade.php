@extends('adminlte::page')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <div class="card mt-3">
                    <div class="card card-teal">
                        <div class="card-header">
                            <i class="nav-icon fas fa-heart nav-icon"></i> {{ __('Marital Statuses') }}
                            <span class="badge badge-info float-right">View Only</span>
                        </div>

                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> This is a view-only section. The marital statuses are
                                predefined and cannot be modified.
                            </div>
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
    @section('plugins.Datatables', true)
    {{ $dataTable->scripts() }}
@endpush
