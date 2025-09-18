@extends('adminlte::page')

@section('title', 'Role Hierarchy')

@section('content_header')
    <div class="row">
        <div class="col-md-6">
            <h1><i class="fas fa-sitemap"></i> Role Hierarchy</h1>
            <p class="text-muted">ASDF-11 Specification - Bus Pass Approval Workflow</p>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Roles
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <!-- Branch/Directorate Hierarchy -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">
                        <i class="fas fa-building"></i> Branch/Directorate Hierarchy
                    </h3>
                </div>
                <div class="card-body">
                    <div class="hierarchy-container">
                        @php
                            $branchRoles = [
                                1 => 'Bus Pass Subject Clerk (Branch)',
                                2 => 'Staff Officer (Branch)',
                                3 => 'Director (Branch)'
                            ];
                        @endphp
                        
                        @foreach($branchRoles as $level => $roleName)
                            @php
                                $role = $roles->where('name', $roleName)->first();
                            @endphp
                            <div class="hierarchy-item mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="hierarchy-level">
                                        <span class="badge badge-primary badge-lg">{{ $level }}</span>
                                    </div>
                                    <div class="hierarchy-content flex-grow-1 ml-3">
                                        <div class="card border-primary">
                                            <div class="card-body py-2">
                                                <h6 class="mb-1">
                                                    {{ $roleName }}
                                                    @if($role)
                                                        <span class="badge badge-success ml-2">Active</span>
                                                    @else
                                                        <span class="badge badge-danger ml-2">Not Created</span>
                                                    @endif
                                                </h6>
                                                @if($role)
                                                    <small class="text-muted">
                                                        {{ $role->users->count() }} users | 
                                                        {{ $role->permissions->count() }} permissions
                                                    </small>
                                                    <div class="mt-1">
                                                        <a href="{{ route('roles.show', $role) }}" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if($level < 3)
                                    <div class="hierarchy-arrow text-center">
                                        <i class="fas fa-arrow-down text-primary"></i>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                        
                        <!-- Forward to DMOV -->
                        <div class="text-center my-3">
                            <div class="badge badge-warning badge-lg">
                                <i class="fas fa-forward"></i> Forward to DMOV
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DMOV Hierarchy -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title">
                        <i class="fas fa-truck"></i> DMOV Hierarchy
                    </h3>
                </div>
                <div class="card-body">
                    <div class="hierarchy-container">
                        @php
                            $dmovRoles = [
                                4 => 'Subject Clerk (DMOV)',
                                5 => 'Staff Officer 2 (DMOV)',
                                6 => 'Staff Officer 1 (DMOV)',
                                7 => 'Col Mov (DMOV)',
                                8 => 'Director (DMOV)',
                                9 => 'Bus Escort (DMOV)'
                            ];
                        @endphp
                        
                        @foreach($dmovRoles as $level => $roleName)
                            @php
                                $role = $roles->where('name', $roleName)->first();
                            @endphp
                            <div class="hierarchy-item mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="hierarchy-level">
                                        <span class="badge badge-success badge-lg">{{ $level }}</span>
                                    </div>
                                    <div class="hierarchy-content flex-grow-1 ml-3">
                                        <div class="card border-success">
                                            <div class="card-body py-2">
                                                <h6 class="mb-1">
                                                    {{ $roleName }}
                                                    @if($role)
                                                        <span class="badge badge-success ml-2">Active</span>
                                                    @else
                                                        <span class="badge badge-danger ml-2">Not Created</span>
                                                    @endif
                                                </h6>
                                                @if($role)
                                                    <small class="text-muted">
                                                        {{ $role->users->count() }} users | 
                                                        {{ $role->permissions->count() }} permissions
                                                    </small>
                                                    <div class="mt-1">
                                                        <a href="{{ route('roles.show', $role) }}" class="btn btn-sm btn-outline-success">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if($level < 9)
                                    <div class="hierarchy-arrow text-center">
                                        <i class="fas fa-arrow-down text-success"></i>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Administrator -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h3 class="card-title">
                        <i class="fas fa-crown"></i> System Administration
                    </h3>
                </div>
                <div class="card-body">
                    @php
                        $systemAdminRole = $roles->where('name', 'System Administrator (DMOV)')->first();
                    @endphp
                    
                    <div class="text-center">
                        <div class="hierarchy-item">
                            <div class="card border-danger" style="max-width: 400px; margin: 0 auto;">
                                <div class="card-body">
                                    <h5 class="mb-2">
                                        <span class="badge badge-danger badge-lg">10</span>
                                        System Administrator (DMOV)
                                        @if($systemAdminRole)
                                            <span class="badge badge-success ml-2">Active</span>
                                        @else
                                            <span class="badge badge-danger ml-2">Not Created</span>
                                        @endif
                                    </h5>
                                    @if($systemAdminRole)
                                        <p class="text-muted mb-2">
                                            {{ $systemAdminRole->users->count() }} users | 
                                            {{ $systemAdminRole->permissions->count() }} permissions
                                        </p>
                                        <p class="text-sm mb-3">
                                            Full system access including user management, role management, 
                                            bus route management, and all administrative functions.
                                        </p>
                                        <a href="{{ route('roles.show', $systemAdminRole) }}" class="btn btn-outline-danger">
                                            <i class="fas fa-eye"></i> View Details
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Workflow Summary -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h3 class="card-title">
                        <i class="fas fa-flow-chart"></i> Approval Workflow Summary
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5><i class="fas fa-building text-primary"></i> Branch/Directorate Process</h5>
                            <ol class="list-group list-group-numbered">
                                <li class="list-group-item">
                                    <strong>Bus Pass Subject Clerk</strong> - Creates and submits application
                                </li>
                                <li class="list-group-item">
                                    <strong>Staff Officer</strong> - Reviews and recommends
                                </li>
                                <li class="list-group-item">
                                    <strong>Director</strong> - Approves and forwards to DMOV
                                </li>
                            </ol>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="fas fa-truck text-success"></i> DMOV Process</h5>
                            <ol class="list-group list-group-numbered" start="4">
                                <li class="list-group-item">
                                    <strong>Subject Clerk</strong> - Receives and processes
                                </li>
                                <li class="list-group-item">
                                    <strong>Staff Officer 2</strong> - Reviews application
                                </li>
                                <li class="list-group-item">
                                    <strong>Staff Officer 1</strong> - Further review
                                </li>
                                <li class="list-group-item">
                                    <strong>Col Mov</strong> - Command approval
                                </li>
                                <li class="list-group-item">
                                    <strong>Director</strong> - Final approval
                                </li>
                                <li class="list-group-item">
                                    <strong>Bus Escort</strong> - Implementation and monitoring
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .hierarchy-container {
            padding: 1rem 0;
        }
        .hierarchy-level {
            min-width: 50px;
        }
        .hierarchy-arrow {
            margin: 0.5rem 0;
        }
        .badge-lg {
            font-size: 1rem;
            padding: 0.5rem 0.75rem;
        }
        .hierarchy-item {
            position: relative;
        }
        .list-group-numbered {
            counter-reset: section;
        }
        .list-group-numbered li {
            counter-increment: section;
        }
        .list-group-numbered li::before {
            content: counter(section) ".";
            font-weight: bold;
            margin-right: 0.5rem;
        }
    </style>
@stop
