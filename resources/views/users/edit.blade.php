@extends('adminlte::page')

@section('title', 'Edit User')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1><i class="fas fa-user-edit"></i> Edit User: {{ $user->name }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf
        @method('PATCH')
        
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">User Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $user->name) }}" 
                                           required>
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', $user->email) }}" 
                                           required>
                                    @error('email')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="regiment_no">Regiment Number</label>
                                    <input type="text" 
                                           class="form-control @error('regiment_no') is-invalid @enderror" 
                                           id="regiment_no" 
                                           name="regiment_no" 
                                           value="{{ old('regiment_no', $user->regiment_no) }}">
                                    @error('regiment_no')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="rank">Rank</label>
                                    <input type="text" 
                                           class="form-control @error('rank') is-invalid @enderror" 
                                           id="rank" 
                                           name="rank" 
                                           value="{{ old('rank', $user->rank) }}">
                                    @error('rank')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="contact_no">Contact Number</label>
                            <input type="text" 
                                   class="form-control @error('contact_no') is-invalid @enderror" 
                                   id="contact_no" 
                                   name="contact_no" 
                                   value="{{ old('contact_no', $user->contact_no) }}">
                            @error('contact_no')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Change Password</h4>
                                <small class="text-muted">Leave blank to keep current password</small>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">New Password</label>
                                            <input type="password" 
                                                   class="form-control @error('password') is-invalid @enderror" 
                                                   id="password" 
                                                   name="password">
                                            @error('password')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password_confirmation">Confirm New Password</label>
                                            <input type="password" 
                                                   class="form-control" 
                                                   id="password_confirmation" 
                                                   name="password_confirmation">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Role Assignment</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Assign Roles <span class="text-danger">*</span></label>
                            @error('roles')
                                <div class="text-danger small mb-2">{{ $message }}</div>
                            @enderror
                            
                            @if($roles->count() > 0)
                                <div class="role-list" style="max-height: 300px; overflow-y: auto;">
                                    @foreach($roles as $role)
                                        <div class="custom-control custom-checkbox mb-2">
                                            <input type="checkbox" 
                                                   class="custom-control-input" 
                                                   id="role_{{ $role->id }}" 
                                                   name="roles[]" 
                                                   value="{{ $role->id }}"
                                                   @if(in_array($role->id, old('roles', $userRoles))) checked @endif>
                                            <label class="custom-control-label" for="role_{{ $role->id }}">
                                                <strong>{{ $role->name }}</strong>
                                                <small class="text-muted d-block">
                                                    {{ $role->permissions->count() }} permissions
                                                </small>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">No roles available. Please create roles first.</p>
                            @endif
                        </div>

                        <div class="form-group">
                            <label>Account Status</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" 
                                       class="custom-control-input" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1" 
                                       @if(old('is_active', $user->is_active)) checked @endif>
                                <label class="custom-control-label" for="is_active">Active Account</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">User Details</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Created:</strong> {{ $user->created_at->format('M d, Y H:i') }}</p>
                        <p><strong>Last Updated:</strong> {{ $user->updated_at->format('M d, Y H:i') }}</p>
                        @if($user->last_login_at)
                            <p><strong>Last Login:</strong> {{ $user->last_login_at->format('M d, Y H:i') }}</p>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-save"></i> Update User
                        </button>
                        <a href="{{ route('users.show', $user) }}" class="btn btn-info btn-block">
                            <i class="fas fa-eye"></i> View User
                        </a>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop

@section('css')
    <style>
        .role-list {
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            padding: 0.75rem;
            background-color: #f8f9fa;
        }
    </style>
@stop
