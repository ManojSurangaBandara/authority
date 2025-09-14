@extends('adminlte::page')

@section('title', 'SLCMP In-charge Assignment Details')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1><i class="fas fa-shield-alt"></i> SLCMP In-charge Assignment Details</h1>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-teal">
                    <div class="card-header">
                        <i class="nav-icon fas fa-shield-alt nav-icon"></i> {{ __('SLCMP In-charge Assignment Details') }}
                        <div class="card-tools">
                            <a href="{{ route('slcmp-incharge-assignments.index') }}" class="btn btn-dark btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                            <a href="{{ route('slcmp-incharge-assignments.edit', $slcmpInchargeAssignment->id) }}" class="btn btn-warning btn-sm">
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
                                            <dd class="col-sm-8">{{ $slcmpInchargeAssignment->id }}</dd>

                                            <dt class="col-sm-4">Bus Route:</dt>
                                            <dd class="col-sm-8">
                                                <strong>{{ $slcmpInchargeAssignment->busRoute->name ?? 'N/A' }}</strong>
                                                @if($slcmpInchargeAssignment->busRoute)
                                                    <br><small class="text-muted">{{ $slcmpInchargeAssignment->busRoute->description }}</small>
                                                @endif
                                            </dd>

                                            <dt class="col-sm-4">Bus No:</dt>
                                            <dd class="col-sm-8">
                                                @if($slcmpInchargeAssignment->busRoute && $slcmpInchargeAssignment->busRoute->bus)
                                                    <span class="badge badge-primary">{{ $slcmpInchargeAssignment->busRoute->bus->no }}</span>
                                                    <br><small class="text-muted">{{ $slcmpInchargeAssignment->busRoute->bus->name ?? '' }} ({{ $slcmpInchargeAssignment->busRoute->bus->type->name ?? '' }})</small>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </dd>

                                            <dt class="col-sm-4">Assigned Date:</dt>
                                            <dd class="col-sm-8">{{ $slcmpInchargeAssignment->assigned_date ? $slcmpInchargeAssignment->assigned_date->format('d M Y') : 'N/A' }}</dd>

                                            <dt class="col-sm-4">End Date:</dt>
                                            <dd class="col-sm-8">{{ $slcmpInchargeAssignment->end_date ? $slcmpInchargeAssignment->end_date->format('d M Y') : 'Not Set' }}</dd>

                                            <dt class="col-sm-4">Status:</dt>
                                            <dd class="col-sm-8">{!! $slcmpInchargeAssignment->status_badge !!}</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>

                            <!-- SLCMP Information -->
                            <div class="col-md-6">
                                <div class="card card-outline card-success">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-user-shield"></i> SLCMP Information</h3>
                                    </div>
                                    <div class="card-body">
                                        <dl class="row">
                                            <dt class="col-sm-4">Regiment No:</dt>
                                            <dd class="col-sm-8"><strong>{{ $slcmpInchargeAssignment->slcmp_regiment_no }}</strong></dd>

                                            <dt class="col-sm-4">Rank:</dt>
                                            <dd class="col-sm-8">{{ $slcmpInchargeAssignment->slcmp_rank }}</dd>

                                            <dt class="col-sm-4">Name:</dt>
                                            <dd class="col-sm-8"><strong>{{ $slcmpInchargeAssignment->slcmp_name }}</strong></dd>

                                            <dt class="col-sm-4">Contact No:</dt>
                                            <dd class="col-sm-8">
                                                @if($slcmpInchargeAssignment->slcmp_contact_no)
                                                    <a href="tel:{{ $slcmpInchargeAssignment->slcmp_contact_no }}" class="text-primary">
                                                        <i class="fas fa-phone"></i> {{ $slcmpInchargeAssignment->slcmp_contact_no }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">Not provided</span>
                                                @endif
                                            </dd>
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
                                                        <span class="info-box-number">{{ $slcmpInchargeAssignment->assigned_date ? $slcmpInchargeAssignment->assigned_date->format('d M Y') : 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="info-box bg-warning">
                                                    <span class="info-box-icon"><i class="fas fa-calendar-minus"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">End Date</span>
                                                        <span class="info-box-number">{{ $slcmpInchargeAssignment->end_date ? $slcmpInchargeAssignment->end_date->format('d M Y') : 'Ongoing' }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="info-box bg-success">
                                                    <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Duration</span>
                                                        <span class="info-box-number">
                                                            @if($slcmpInchargeAssignment->assigned_date)
                                                                @if($slcmpInchargeAssignment->end_date)
                                                                    {{ $slcmpInchargeAssignment->assigned_date->diffInDays($slcmpInchargeAssignment->end_date) }} days
                                                                @else
                                                                    {{ $slcmpInchargeAssignment->assigned_date->diffInDays(now()) }} days
                                                                @endif
                                                            @else
                                                                N/A
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="info-box bg-{{ $slcmpInchargeAssignment->status == 'active' ? 'success' : 'secondary' }}">
                                                    <span class="info-box-icon"><i class="fas fa-{{ $slcmpInchargeAssignment->status == 'active' ? 'check-circle' : 'pause-circle' }}"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Status</span>
                                                        <span class="info-box-number">{{ ucfirst($slcmpInchargeAssignment->status) }}</span>
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
                                            <dt class="col-sm-2">Created At:</dt>
                                            <dd class="col-sm-4">{{ $slcmpInchargeAssignment->created_at ? $slcmpInchargeAssignment->created_at->format('d M Y, h:i A') : 'N/A' }}</dd>

                                            <dt class="col-sm-2">Last Updated:</dt>
                                            <dd class="col-sm-4">{{ $slcmpInchargeAssignment->updated_at ? $slcmpInchargeAssignment->updated_at->format('d M Y, h:i A') : 'N/A' }}</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <a href="{{ route('slcmp-incharge-assignments.edit', $slcmpInchargeAssignment->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit Assignment
                        </a>
                        <a href="{{ route('slcmp-incharge-assignments.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                        
                        <!-- Delete Button with Confirmation -->
                        <form action="{{ route('slcmp-incharge-assignments.destroy', $slcmpInchargeAssignment->id) }}" method="POST" class="d-inline" id="deleteForm">
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
                text: "This SLCMP In-charge assignment will be permanently deleted!",
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
