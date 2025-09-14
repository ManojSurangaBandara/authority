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
                            <a href="{{ route('bus-driver-assignments.edit', $bus_driver_assignment->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('bus-driver-assignments.index') }}" class="btn btn-dark btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Assignment Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-3"><i class="fas fa-info-circle text-info"></i> Assignment Information</h5>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <tr>
                                            <th width="40%">Assignment ID</th>
                                            <td>{{ $bus_driver_assignment->id }}</td>
                                        </tr>
                                        <tr>
                                            <th>Bus Route</th>
                                            <td>
                                                <strong>{{ $bus_driver_assignment->busRoute->name ?? 'N/A' }}</strong>
                                                @if($bus_driver_assignment->busRoute && $bus_driver_assignment->busRoute->bus)
                                                    <br><small class="text-muted">{{ $bus_driver_assignment->busRoute->bus->name }} ({{ $bus_driver_assignment->busRoute->bus->type->name ?? 'N/A' }})</small>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Bus No</th>
                                            <td>
                                                @if($bus_driver_assignment->busRoute && $bus_driver_assignment->busRoute->bus)
                                                    <span class="badge badge-primary">{{ $bus_driver_assignment->busRoute->bus->no }}</span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                @if($bus_driver_assignment->status == 'active')
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check-circle"></i> Active
                                                    </span>
                                                @else
                                                    <span class="badge badge-danger">
                                                        <i class="fas fa-times-circle"></i> Inactive
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h5 class="mb-3"><i class="fas fa-user-tie text-primary"></i> Driver Information</h5>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <tr>
                                            <th width="40%">Regiment No</th>
                                            <td><span class="badge badge-info">{{ $bus_driver_assignment->driver_regiment_no }}</span></td>
                                        </tr>
                                        <tr>
                                            <th>Rank</th>
                                            <td><strong>{{ $bus_driver_assignment->driver_rank }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Name</th>
                                            <td><strong>{{ $bus_driver_assignment->driver_name }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Contact No</th>
                                            <td>
                                                <i class="fas fa-phone text-success"></i>
                                                <a href="tel:{{ $bus_driver_assignment->driver_contact_no }}">{{ $bus_driver_assignment->driver_contact_no }}</a>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Date Information -->
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="mb-3"><i class="fas fa-calendar text-warning"></i> Date Information</h5>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-success">
                                                <i class="fas fa-calendar-plus"></i>
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Assigned Date</span>
                                                <span class="info-box-number">{{ $bus_driver_assignment->assigned_date->format('d M Y') }}</span>
                                                <div class="progress">
                                                    <div class="progress-bar bg-success" style="width: 100%"></div>
                                                </div>
                                                <span class="progress-description">
                                                    {{ $bus_driver_assignment->assigned_date->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-{{ $bus_driver_assignment->end_date ? 'warning' : 'secondary' }}">
                                                <i class="fas fa-calendar-minus"></i>
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">End Date</span>
                                                <span class="info-box-number">
                                                    @if($bus_driver_assignment->end_date)
                                                        {{ $bus_driver_assignment->end_date->format('d M Y') }}
                                                    @else
                                                        <span class="text-muted">Not Set</span>
                                                    @endif
                                                </span>
                                                <div class="progress">
                                                    <div class="progress-bar bg-{{ $bus_driver_assignment->end_date ? 'warning' : 'secondary' }}" style="width: 100%"></div>
                                                </div>
                                                <span class="progress-description">
                                                    @if($bus_driver_assignment->end_date)
                                                        {{ $bus_driver_assignment->end_date->diffForHumans() }}
                                                    @else
                                                        Open-ended assignment
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-info">
                                                <i class="fas fa-clock"></i>
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Duration</span>
                                                <span class="info-box-number">
                                                    @if($bus_driver_assignment->end_date)
                                                        {{ $bus_driver_assignment->assigned_date->diffInDays($bus_driver_assignment->end_date) }} days
                                                    @else
                                                        {{ $bus_driver_assignment->assigned_date->diffInDays(now()) }} days
                                                    @endif
                                                </span>
                                                <div class="progress">
                                                    <div class="progress-bar bg-info" style="width: 100%"></div>
                                                </div>
                                                <span class="progress-description">
                                                    @if($bus_driver_assignment->end_date)
                                                        Total assignment period
                                                    @else
                                                        Days since assignment
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Record Information -->
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="mb-3"><i class="fas fa-database text-secondary"></i> Record Information</h5>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <tr>
                                            <th width="20%">Created At</th>
                                            <td>{{ $bus_driver_assignment->created_at->format('d M Y, h:i A') }} ({{ $bus_driver_assignment->created_at->diffForHumans() }})</td>
                                        </tr>
                                        <tr>
                                            <th>Last Updated</th>
                                            <td>{{ $bus_driver_assignment->updated_at->format('d M Y, h:i A') }} ({{ $bus_driver_assignment->updated_at->diffForHumans() }})</td>
                                        </tr>
                                        @if($bus_driver_assignment->created_by)
                                        <tr>
                                            <th>Created By</th>
                                            <td>{{ $bus_driver_assignment->created_by }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('bus-driver-assignments.edit', $bus_driver_assignment->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Edit Assignment
                                </a>
                                <a href="{{ route('bus-driver-assignments.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
                            </div>
                            <div class="col-md-6 text-right">
                                <form action="{{ route('bus-driver-assignments.destroy', $bus_driver_assignment->id) }}" method="POST"
                                      style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this assignment?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> Delete Assignment
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .info-box {
            margin-bottom: 1rem;
        }

        .info-box-content {
            padding: 5px 10px;
        }

        .progress {
            height: 2px;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .badge {
            font-size: 0.85em;
        }
    </style>
@stop

@section('js')
    <script>
        // Add any page-specific JavaScript here
        console.log('Bus Driver Assignment Details page loaded');
    </script>
@stop
