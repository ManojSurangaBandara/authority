@extends('adminlte::page')

@section('title', 'Role Details - ' . $role->name)

@section('content_header')
    <div class="row">
        <div class="col-md-6">
            <h1><i class="fas fa-user-shield"></i> Role Details</h1>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Roles
            </a>
            <a href="{{ route('roles.edit', $role) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Role
            </a>
            <a href="{{ route('roles.permissions', $role) }}" class="btn btn-warning">
                <i class="fas fa-key"></i> Manage Permissions
            </a>
        </div>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <!-- Role Information -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Role Information
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">Role Name:</th>
                            <td>
                                <strong>{{ $role->name }}</strong>
                                @if($role->name === 'System Administrator (DMOV)')
                                    <span class="badge badge-danger ml-2">Super Admin</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Guard Name:</th>
                            <td><code>{{ $role->guard_name }}</code></td>
                        </tr>
                        <tr>
                            <th>Created Date:</th>
                            <td>{{ $role->created_at->format('F d, Y \a\t g:i A') }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated:</th>
                            <td>{{ $role->updated_at->format('F d, Y \a\t g:i A') }}</td>
                        </tr>
                        <tr>
                            <th>Total Permissions:</th>
                            <td>
                                <span class="badge badge-info">
                                    {{ $role->permissions->count() }} permissions
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Total Users:</th>
                            <td>
                                <span class="badge badge-secondary">
                                    {{ $role->users->count() }} users
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Users with this Role -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h3 class="card-title">
                        <i class="fas fa-users"></i> Users with this Role
                    </h3>
                </div>
                <div class="card-body">
                    @if($role->users->count() > 0)
                        <div class="list-group">
                            @foreach($role->users as $user)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $user->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $user->email }}</small>
                                        @if($user->regiment_no)
                                            <br>
                                            <small class="text-info">{{ $user->rank }} - {{ $user->regiment_no }}</small>
                                        @endif
                                    </div>
                                    <div>
                                        @if($user->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                        <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-primary ml-2">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No users are currently assigned to this role.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Permissions Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title">
                        <i class="fas fa-key"></i> Role Permissions
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('roles.permissions', $role) }}" class="btn btn-sm btn-light">
                            <i class="fas fa-edit"></i> Manage Permissions
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($role->permissions->count() > 0)
                        @php
                            $groupedPermissions = $role->permissions->groupBy(function($permission) {
                                $parts = explode('_', $permission->name);
                                return ucfirst($parts[count($parts) - 1] ?? 'General');
                            });
                        @endphp
                        
                        <div class="row">
                            @foreach($groupedPermissions as $category => $permissions)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card border-success">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">
                                                <i class="fas fa-folder"></i> {{ $category }} Permissions
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach($permissions as $permission)
                                                <div class="mb-1">
                                                    <i class="fas fa-check text-success"></i>
                                                    {{ ucfirst(str_replace('_', ' ', $permission->name)) }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> 
                            This role has no permissions assigned. 
                            <a href="{{ route('roles.permissions', $role) }}" class="alert-link">
                                Click here to assign permissions.
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .list-group-item {
            border: 1px solid #dee2e6;
            margin-bottom: 0.5rem;
            border-radius: 0.375rem;
        }
        .card-header h6 {
            font-weight: bold;
        }
    </style>
@stop
