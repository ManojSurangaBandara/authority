@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <div class="row">
        <div class="col-md-6">
            <h1>
                <i class="fas fa-tachometer-alt"></i> Dashboard
                @auth
                    @if(auth()->user()->isBranchUser())
                        <small class="text-muted">- Branch/Directorate</small>
                    @elseif(auth()->user()->isMovementUser())
                        <small class="text-muted">- Directorate of Movement</small>
                    @endif
                @endauth
            </h1>
        </div>
        <div class="col-md-6 text-right">
            <div class="text-muted">
                Welcome back, <strong>{{ auth()->user()->name }}</strong>
                @foreach(auth()->user()->roles as $role)
                    <span class="badge badge-primary ml-1">{{ $role->name }}</span>
                @endforeach
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <!-- Role-specific welcome message -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Welcome to Authority Management System
                    </h3>
                </div>
                <div class="card-body">
                    @auth
                        @if(auth()->user()->hasRole('Bus Pass Subject Clerk (Branch)'))
                            <p class="lead">Welcome to the Branch Bus Pass Management System. You can create and manage bus pass applications for your establishment.</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <a href="{{ route('bus-pass-applications.index') }}" class="btn btn-primary btn-lg btn-block">
                                        <i class="fas fa-id-card"></i> Manage Bus Pass Applications
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="{{ route('bus-pass-applications.create') }}" class="btn btn-success btn-lg btn-block">
                                        <i class="fas fa-plus"></i> Create New Application
                                    </a>
                                </div>
                            </div>
                        @elseif(auth()->user()->hasRole('Staff Officer (Branch)'))
                            <p class="lead">Welcome Staff Officer. You can review and approve bus pass applications from your branch.</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <a href="{{ route('bus-pass-approvals.index') }}" class="btn btn-primary btn-lg btn-block">
                                        <i class="fas fa-check-circle"></i> Review Applications
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="{{ route('bus-pass-applications.index') }}" class="btn btn-info btn-lg btn-block">
                                        <i class="fas fa-list"></i> View All Applications
                                    </a>
                                </div>
                            </div>
                        @elseif(auth()->user()->hasRole('Director (Branch)'))
                            <p class="lead">Welcome Director. You have final approval authority for bus pass applications from your directorate.</p>
                            <div class="row">
                                <div class="col-md-4">
                                    <a href="{{ route('bus-pass-approvals.index') }}" class="btn btn-primary btn-lg btn-block">
                                        <i class="fas fa-gavel"></i> Final Approvals
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="{{ route('bus-pass-applications.index') }}" class="btn btn-info btn-lg btn-block">
                                        <i class="fas fa-list"></i> All Applications
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="{{ route('rejected-applications.index') }}" class="btn btn-warning btn-lg btn-block">
                                        <i class="fas fa-chart-bar"></i> Reports
                                    </a>
                                </div>
                            </div>
                        @elseif(auth()->user()->isMovementUser())
                            <p class="lead">Welcome to Directorate of Movement. You can manage bus operations and approve applications forwarded from branches.</p>
                            <div class="row">
                                <div class="col-md-3">
                                    <a href="{{ route('bus-pass-approvals.index') }}" class="btn btn-primary btn-lg btn-block">
                                        <i class="fas fa-check-circle"></i> Approvals
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="{{ route('buses.index') }}" class="btn btn-success btn-lg btn-block">
                                        <i class="fas fa-bus"></i> Manage Buses
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="{{ route('drivers.index') }}" class="btn btn-info btn-lg btn-block">
                                        <i class="fas fa-user-tie"></i> Drivers
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="{{ route('bus-routes.index') }}" class="btn btn-warning btn-lg btn-block">
                                        <i class="fas fa-route"></i> Routes
                                    </a>
                                </div>
                            </div>
                        @elseif(auth()->user()->hasRole('System Administrator (DMOV)'))
                            <p class="lead">Welcome System Administrator. You have full access to manage users, roles, and system configuration.</p>
                            <div class="row">
                                <div class="col-md-3">
                                    <a href="{{ route('users.index') }}" class="btn btn-danger btn-lg btn-block">
                                        <i class="fas fa-users"></i> Manage Users
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="{{ route('roles.index') }}" class="btn btn-warning btn-lg btn-block">
                                        <i class="fas fa-user-shield"></i> Manage Roles
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="{{ route('establishment.index') }}" class="btn btn-info btn-lg btn-block">
                                        <i class="fas fa-building"></i> Establishments
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="{{ route('roles.hierarchy') }}" class="btn btn-secondary btn-lg btn-block">
                                        <i class="fas fa-sitemap"></i> Role Hierarchy
                                    </a>
                                </div>
                            </div>
                        @else
                            <p class="lead">Welcome to the Authority Management System. Please contact your administrator if you need access to specific features.</p>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Row -->
    <div class="row mt-4">
        @auth
            @if(auth()->user()->hasRole('Bus Pass Subject Clerk (Branch)') || auth()->user()->hasRole('Staff Officer (Branch)') || auth()->user()->hasRole('Director (Branch)'))
                <!-- Branch user stats -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ \App\Models\BusPassApplication::where('establishment_id', auth()->user()->establishment_id)->count() }}</h3>
                            <p>My Applications</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <a href="{{ route('bus-pass-applications.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ \App\Models\BusPassApplication::where('establishment_id', auth()->user()->establishment_id)->where('status', 'approved')->count() }}</h3>
                            <p>Approved</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <a href="{{ route('bus-pass-applications.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ \App\Models\BusPassApplication::where('establishment_id', auth()->user()->establishment_id)->whereIn('status', ['pending_subject_clerk', 'pending_staff_officer_branch', 'pending_director_branch'])->count() }}</h3>
                            <p>Pending</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <a href="{{ route('bus-pass-applications.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ \App\Models\BusPassApplication::where('establishment_id', auth()->user()->establishment_id)->where('status', 'rejected')->count() }}</h3>
                            <p>Rejected</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <a href="{{ route('rejected-applications.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            @elseif(auth()->user()->isMovementUser())
                <!-- Movement user stats -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ \App\Models\Bus::count() }}</h3>
                            <p>Total Buses</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-bus"></i>
                        </div>
                        <a href="{{ route('buses.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ \App\Models\Driver::count() }}</h3>
                            <p>Active Drivers</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <a href="{{ route('drivers.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ \App\Models\BusRoute::count() }}</h3>
                            <p>Bus Routes</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-route"></i>
                        </div>
                        <a href="{{ route('bus-routes.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ \App\Models\BusPassApplication::whereIn('status', ['pending_subject_clerk_mov', 'pending_staff_officer_2_mov', 'pending_staff_officer_1_mov', 'pending_col_mov', 'pending_director_mov'])->count() }}</h3>
                            <p>Pending Approvals</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <a href="{{ route('bus-pass-approvals.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            @endif
        @endauth
    </div>
@stop

@section('css')
<style>
    .small-box .icon {
        color: rgba(255,255,255,0.8);
    }
    .card-body .btn-block {
        margin-bottom: 10px;
    }
</style>
@stop