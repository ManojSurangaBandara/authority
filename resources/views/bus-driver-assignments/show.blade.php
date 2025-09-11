@extends('adminlte::page')

@section('title', 'Bus Driver Assignment Details')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1><i class="fas fa-user-tie"></i> Bus Driver Assignment Details</h1>
        </div>

    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-teal">
                    <div class="card-header">
                        <i class="nav-icon fas fa-user-tie nav-icon"></i> {{ __('Bus Driver Assignment Details') }}
                        <div class="card-tools">
                            <a href="{{ route('bus-driver-assignments.index') }}" class="btn btn-dark btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5><i class="fas fa-route"></i> Bus Route Information</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Route Name:</strong></td>
                                        <td>{{ $bus_driver_assignment->busRoute->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Bus No:</strong></td>
                                        <td>{{ $bus_driver_assignment->busRoute->bus->no ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Bus Name:</strong></td>
                                        <td>{{ $bus_driver_assignment->busRoute->bus->name ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-6">
                                <h5><i class="fas fa-user"></i> Driver Information</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Regiment No:</strong></td>
                                        <td>{{ $bus_driver_assignment->driver_regiment_no }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Rank:</strong></td>
                                        <td>{{ $bus_driver_assignment->driver_rank }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Name:</strong></td>
                                        <td>{{ $bus_driver_assignment->driver_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Contact No:</strong></td>
                                        <td>{{ $bus_driver_assignment->driver_contact_no }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h5><i class="fas fa-calendar"></i> Assignment Details</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Assignment Date:</strong></td>
                                        <td>{{ $bus_driver_assignment->assigned_date->format('d M Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>End Date:</strong></td>
                                        <td>{{ $bus_driver_assignment->end_date ? $bus_driver_assignment->end_date->format('d M Y') : 'Ongoing' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>{!! $bus_driver_assignment->status_badge !!}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Created By:</strong></td>
                                        <td>{{ $bus_driver_assignment->created_by ?? 'System' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Created At:</strong></td>
                                        <td>{{ $bus_driver_assignment->created_at->format('d M Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <a href="{{ route('bus-driver-assignments.edit', $bus_driver_assignment) }}"
                            class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit Assignment
                        </a>
                        <a href="{{ route('bus-driver-assignments.index') }}" class="btn btn-secondary">
                            <i class="fas fa-list"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop
