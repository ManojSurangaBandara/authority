@extends('adminlte::page')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-12">

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
{{--
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif --}}
            <div class="card mt-3">
                <div class="card card-teal">
                <div class="card-header"><i class="nav-icon fa fa fa-cogs nav-icon"></i> {{ __('Bus') }}                    <a href="{{ route('buses.create') }}" class="btn btn-sm btn-primary float-right">Add New Bus</a>
                </div>

                <div class="card-body">


            <div class="table-responsive">
            {{ $dataTable->table() }}

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




