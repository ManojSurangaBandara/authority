@extends('adminlte::page')

@section('title', 'User Details')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1><i class="fas fa-user"></i> User Details: {{ $user->name }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                <li class="breadcrumb-item active">{{ $user->name }}</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Personal Information</h3>
                    <div class="card-tools">
                        @can('manage_user_accounts')
                            @if($user->id !== auth()->user()->id)
                                <button type="button" class="btn btn-sm btn-warning" onclick="resetPasswordModal()">
                                    <i class="fas fa-key"></i> Reset Password
                                </button>
                            @endif
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Edit User
                            </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Full Name:</th>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th>Regiment Number:</th>
                                    <td>{{ $user->regiment_no ?? 'Not specified' }}</td>
                                </tr>
                                <tr>
                                    <th>Rank:</th>
                                    <td>{{ $user->rank ?? 'Not specified' }}</td>
                                </tr>
                                <tr>
                                    <th>Contact Number:</th>
                                    <td>{{ $user->contact_no ?? 'Not specified' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Account Status:</th>
                                    <td>
                                        @if($user->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Email Verified:</th>
                                    <td>
                                        @if($user->email_verified_at)
                                            <span class="badge badge-success">Verified</span>
                                            <small class="text-muted d-block">{{ $user->email_verified_at->format('M d, Y H:i') }}</small>
                                        @else
                                            <span class="badge badge-warning">Not Verified</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Last Login:</th>
                                    <td>
                                        @if($user->last_login_at)
                                            {{ $user->last_login_at->format('M d, Y H:i') }}
                                        @else
                                            <span class="text-muted">Never logged in</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created:</th>
                                    <td>{{ $user->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated:</th>
                                    <td>{{ $user->updated_at->format('M d, Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">User Permissions</h3>
                </div>
                <div class="card-body">
                    @if($user->getAllPermissions()->count() > 0)
                        <div class="row">
                            @foreach($user->getAllPermissions()->groupBy(function($permission) {
                                $parts = explode('_', $permission->name);
                                return ucfirst($parts[count($parts) - 1] ?? 'General');
                            }) as $category => $permissions)
                                <div class="col-md-6 mb-3">
                                    <h5>{{ $category }}</h5>
                                    @foreach($permissions as $permission)
                                        <span class="badge badge-secondary badge-sm mr-1 mb-1">{{ $permission->name }}</span>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">This user has no permissions assigned.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Assigned Roles</h3>
                </div>
                <div class="card-body">
                    @if($user->roles->count() > 0)
                        @foreach($user->roles as $role)
                            <div class="role-item mb-3 p-3 border rounded">
                                <h5 class="mb-1">{{ $role->name }}</h5>
                                <p class="text-muted mb-2">{{ $role->permissions->count() }} permissions</p>
                                
                                @if($role->permissions->count() > 0)
                                    <details>
                                        <summary class="text-primary" style="cursor: pointer;">View Permissions</summary>
                                        <div class="mt-2">
                                            @foreach($role->permissions as $permission)
                                                <span class="badge badge-light badge-sm mr-1 mb-1">{{ $permission->name }}</span>
                                            @endforeach
                                        </div>
                                    </details>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">No roles assigned to this user.</p>
                        @can('manage_user_accounts')
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Assign Roles
                            </a>
                        @endcan
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    @can('manage_user_accounts')
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-primary btn-block mb-2">
                            <i class="fas fa-edit"></i> Edit User
                        </a>
                        
                        @if($user->id !== auth()->user()->id)
                            <button type="button" 
                                    class="btn btn-{{ $user->is_active ? 'secondary' : 'success' }} btn-block mb-2" 
                                    onclick="toggleUserStatus({{ $user->id }})">
                                <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }}"></i> 
                                {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                            
                            <button type="button" class="btn btn-warning btn-block mb-2" onclick="resetPasswordModal()">
                                <i class="fas fa-key"></i> Reset Password
                            </button>
                            
                            <button type="button" class="btn btn-danger btn-block" onclick="deleteUser({{ $user->id }})">
                                <i class="fas fa-trash"></i> Delete User
                            </button>
                        @endif
                    @endcan
                    
                    <a href="{{ route('users.index') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-arrow-left"></i> Back to Users
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Reset Password Modal -->
    <div class="modal fade" id="resetPasswordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('users.reset-password', $user) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header">
                        <h4 class="modal-title">Reset Password</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>
                        <div class="form-group">
                            <label for="new_password_confirmation">Confirm New Password</label>
                            <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Reset Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Confirm Delete</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this user? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Toggle Status Form -->
    <form id="toggleStatusForm" method="POST" style="display: none;">
        @csrf
        @method('PATCH')
    </form>
@stop

@section('css')
    <style>
        .role-item {
            background-color: #f8f9fa;
        }
        details summary {
            outline: none;
        }
    </style>
@stop

@section('js')
    <script>
        function resetPasswordModal() {
            $('#resetPasswordModal').modal('show');
        }

        function deleteUser(userId) {
            $('#deleteForm').attr('action', '/users/' + userId);
            $('#deleteModal').modal('show');
        }

        function toggleUserStatus(userId) {
            if (confirm('Are you sure you want to change this user\'s status?')) {
                $('#toggleStatusForm').attr('action', '/users/' + userId + '/toggle-status');
                $('#toggleStatusForm').submit();
            }
        }
    </script>
@stop
