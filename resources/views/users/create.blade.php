@extends('adminlte::page')

@section('title', 'Create User')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1><i class="fas fa-user-plus"></i> Create New User</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <form action="{{ route('users.store') }}" method="POST">
        @csrf

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
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="e_no">E No <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('e_no') is-invalid @enderror"
                                        id="e_no" name="e_no" value="{{ old('e_no') }}" required>
                                    @error('e_no')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">
                                        E No will be used for login authentication
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="regiment_no">Regiment Number</label>
                                    <input type="text" class="form-control @error('regiment_no') is-invalid @enderror"
                                        id="regiment_no" name="regiment_no" value="{{ old('regiment_no') }}">
                                    @error('regiment_no')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="rank">Rank</label>
                                    <input type="text" class="form-control @error('rank') is-invalid @enderror"
                                        id="rank" name="rank" value="{{ old('rank') }}">
                                    @error('rank')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="contact_no">Contact Number</label>
                            <input type="text" class="form-control @error('contact_no') is-invalid @enderror"
                                id="contact_no" name="contact_no" value="{{ old('contact_no') }}">
                            @error('contact_no')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="establishment_id">Branch/Directorate <span class="text-danger">*</span></label>
                            <select class="form-control @error('establishment_id') is-invalid @enderror"
                                id="establishment_id" name="establishment_id" required>
                                <option value="">Select Branch/Directorate</option>
                                @foreach ($establishments as $establishment)
                                    <option value="{{ $establishment->id }}"
                                        {{ old('establishment_id') == $establishment->id ? 'selected' : '' }}>
                                        {{ $establishment->name }}
                                        @if ($establishment->location)
                                            ({{ $establishment->location }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('establishment_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                Select the branch or directorate this user belongs to. This is required for bus pass
                                applications.
                            </small>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Authentication:</strong> User will login using their E No and password. Password will be
                            authenticated via API.
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

                            @if ($roles->count() > 0)
                                <div class="role-list" style="max-height: 300px; overflow-y: auto;">
                                    @foreach ($roles as $role)
                                        <div class="custom-control custom-checkbox mb-2">
                                            <input type="checkbox" class="custom-control-input"
                                                id="role_{{ $role->id }}" name="roles[]" value="{{ $role->id }}"
                                                @if (is_array(old('roles')) && in_array($role->id, old('roles'))) checked @endif>
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
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active"
                                    value="1" @if (old('is_active', true)) checked @endif>
                                <label class="custom-control-label" for="is_active">Active Account</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-save"></i> Create User
                        </button>
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

@section('js')
    <script>
        $(document).ready(function() {
            // E No validation or other JavaScript can be added here if needed
            console.log('User creation form loaded with E No authentication');
        });
    </script>
@stop
