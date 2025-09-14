@extends('adminlte::page')

@section('title', 'Bus Filling Station Assignment Details')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1><i class="fas fa-gas-pump"></i> Bus Filling Station Assignment Details</h1>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-teal">
                    <div class="card-header">
                        <i class="nav-icon fas fa-gas-pump nav-icon"></i> {{ __('Bus Filling Station Assignment Details') }}
                        <div class="card-tools">
                            <a href="{{ route('bus-filling-station-assignments.index') }}" class="btn btn-dark btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                            <a href="{{ route('bus-filling-station-assignments.edit', $busFillingStationAssignment->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <!-- Assignment Information -->
                            <div class="col-md-6">
                                <div class="card card-outline card-info">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-info-circle"></i> Assignment Information</h3>
                                    </div>
                                    <div class="card-body">
                                        <dl class="row">
                                            <dt class="col-sm-4">Assignment ID:</dt>
                                            <dd class="col-sm-8">{{ $busFillingStationAssignment->id }}</dd>

                                            <dt class="col-sm-4">Filling Station:</dt>
                                            <dd class="col-sm-8"><strong>{{ $busFillingStationAssignment->fillingStation ? $busFillingStationAssignment->fillingStation->name : 'N/A' }}</strong></dd>

                                            <dt class="col-sm-4">Assigned Date:</dt>
                                            <dd class="col-sm-8">{{ $busFillingStationAssignment->assigned_date ? $busFillingStationAssignment->assigned_date->format('d M Y') : 'N/A' }}</dd>

                                            <dt class="col-sm-4">End Date:</dt>
                                            <dd class="col-sm-8">{{ $busFillingStationAssignment->end_date ? $busFillingStationAssignment->end_date->format('d M Y') : 'Not Set' }}</dd>

                                            <dt class="col-sm-4">Status:</dt>
                                            <dd class="col-sm-8">{!! $busFillingStationAssignment->status_badge !!}</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>

                            <!-- Bus Information -->
                            <div class="col-md-6">
                                <div class="card card-outline card-success">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-bus"></i> Bus Information</h3>
                                    </div>
                                    <div class="card-body">
                                        <dl class="row">
                                            <dt class="col-sm-4">Bus Name:</dt>
                                            <dd class="col-sm-8"><strong>{{ $busFillingStationAssignment->bus->name ?? 'N/A' }}</strong></dd>

                                            <dt class="col-sm-4">Bus No:</dt>
                                            <dd class="col-sm-8">
                                                @if($busFillingStationAssignment->bus)
                                                    <span class="badge badge-primary">{{ $busFillingStationAssignment->bus->no }}</span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </dd>

                                            <dt class="col-sm-4">Bus Type:</dt>
                                            <dd class="col-sm-8">{{ $busFillingStationAssignment->bus && $busFillingStationAssignment->bus->type ? $busFillingStationAssignment->bus->type->name : 'N/A' }}</dd>

                                            <dt class="col-sm-4">Capacity:</dt>
                                            <dd class="col-sm-8">{{ $busFillingStationAssignment->bus->no_of_seats ?? 'N/A' }} seats</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Assignment Duration & Status -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card card-outline card-warning">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-calendar-alt"></i> Assignment Duration & Status</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="info-box bg-info">
                                                    <span class="info-box-icon"><i class="fas fa-calendar-plus"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Start Date</span>
                                                        <span class="info-box-number">{{ $busFillingStationAssignment->assigned_date ? $busFillingStationAssignment->assigned_date->format('d M Y') : 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="info-box bg-warning">
                                                    <span class="info-box-icon"><i class="fas fa-calendar-minus"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">End Date</span>
                                                        <span class="info-box-number">{{ $busFillingStationAssignment->end_date ? $busFillingStationAssignment->end_date->format('d M Y') : 'Ongoing' }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="info-box bg-success">
                                                    <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Duration</span>
                                                        <span class="info-box-number">
                                                            @if($busFillingStationAssignment->assigned_date)
                                                                @if($busFillingStationAssignment->end_date)
                                                                    {{ $busFillingStationAssignment->assigned_date->diffInDays($busFillingStationAssignment->end_date) }} days
                                                                @else
                                                                    {{ $busFillingStationAssignment->assigned_date->diffInDays(now()) }} days
                                                                @endif
                                                            @else
                                                                N/A
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="info-box bg-{{ $busFillingStationAssignment->status == 'active' ? 'success' : 'secondary' }}">
                                                    <span class="info-box-icon"><i class="fas fa-{{ $busFillingStationAssignment->status == 'active' ? 'check-circle' : 'pause-circle' }}"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Status</span>
                                                        <span class="info-box-number">{{ ucfirst($busFillingStationAssignment->status) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Record Timestamps -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card card-outline card-secondary">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-history"></i> Record Information</h3>
                                    </div>
                                    <div class="card-body">
                                        <dl class="row">
                                            <dt class="col-sm-2">Created By:</dt>
                                            <dd class="col-sm-4">{{ $busFillingStationAssignment->created_by ?? 'System' }}</dd>

                                            <dt class="col-sm-2">Created At:</dt>
                                            <dd class="col-sm-4">{{ $busFillingStationAssignment->created_at ? $busFillingStationAssignment->created_at->format('d M Y, h:i A') : 'N/A' }}</dd>

                                            <dt class="col-sm-2">Last Updated:</dt>
                                            <dd class="col-sm-4">{{ $busFillingStationAssignment->updated_at ? $busFillingStationAssignment->updated_at->format('d M Y, h:i A') : 'N/A' }}</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <a href="{{ route('bus-filling-station-assignments.edit', $busFillingStationAssignment->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit Assignment
                        </a>
                        <a href="{{ route('bus-filling-station-assignments.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                        
                        <!-- Delete Button with Confirmation -->
                        <form action="{{ route('bus-filling-station-assignments.destroy', $busFillingStationAssignment->id) }}" method="POST" class="d-inline" id="deleteForm">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                                <i class="fas fa-trash"></i> Delete Assignment
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <style>
        .info-box-number {
            font-size: 1.1rem !important;
        }
        .info-box-text {
            font-size: 0.9rem;
        }
    </style>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Toastr configuration
        toastr.options = {
            closeButton: true,
            progressBar: true,
            timeOut: 5000
        };

        function confirmDelete() {
            Swal.fire({
                title: 'Are you sure?',
                text: "This bus filling station assignment will be permanently deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteForm').submit();
                }
            });
        }

        // Show success message if available
        @if(session('success'))
            toastr.success('{{ session('success') }}');
        @endif

        // Show error message if available
        @if(session('error'))
            toastr.error('{{ session('error') }}');
        @endif
    </script>
@stop
