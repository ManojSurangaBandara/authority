@extends('adminlte::page')

@section('title', 'Bus Driver Assignments')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1><i class="fas fa-user-tie"></i> Bus Driver Assignments</h1>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-teal">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-tie nav-icon"></i> Bus Driver Assignments Management
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('bus-driver-assignments.create') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-plus"></i> New Assignment
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        {!! $dataTable->table(['class' => 'table table-striped table-hover']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    {!! $dataTable->scripts() !!}
@stop
