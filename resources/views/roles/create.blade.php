@extends('adminlte::page')

@section('title', 'Create New Role')

@section('content_header')
    <div class="row">
        <div class="col-md-6">
            <h1><i class="fas fa-plus-circle"></i> Create New Role</h1>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Roles
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-user-shield"></i> Role Information</h3>
        </div>
        
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <h5><i class="fas fa-exclamation-triangle"></i> Validation Errors:</h5>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Role Name -->
                <div class="form-group">
                    <label for="name" class="required">Role Name</label>
                    <input type="text" 
                           class="form-control @error('name') is-invalid @enderror" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}" 
                           placeholder="Enter role name (e.g., Branch Manager, Staff Officer)"
                           required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        Choose a descriptive name that clearly identifies the role's purpose.
                    </small>
                </div>

                <!-- Permissions Section -->
                <div class="form-group">
                    <label class="mb-3">
                        <i class="fas fa-key"></i> Assign Permissions
                        <small class="text-muted">(Select the permissions this role should have)</small>
                    </label>
                    
                    @if($permissions->isNotEmpty())
                        <div class="row">
                            @foreach($permissions as $category => $categoryPermissions)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card border-info">
                                        <div class="card-header bg-info text-white">
                                            <h6 class="mb-0">
                                                <i class="fas fa-folder"></i> {{ $category }} Permissions
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach($categoryPermissions as $permission)
                                                <div class="form-check">
                                                    <input type="checkbox" 
                                                           class="form-check-input" 
                                                           id="permission_{{ $permission->id }}" 
                                                           name="permissions[]" 
                                                           value="{{ $permission->id }}"
                                                           {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                        {{ ucfirst(str_replace('_', ' ', $permission->name)) }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Bulk Select Options -->
                        <div class="mt-3">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllPermissions()">
                                <i class="fas fa-check-square"></i> Select All
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAllPermissions()">
                                <i class="fas fa-square"></i> Deselect All
                            </button>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No permissions available. 
                            Please ensure permissions are seeded in the database.
                        </div>
                    @endif
                </div>
            </div>

            <div class="card-footer">
                <div class="row">
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Create Role
                        </button>
                        <a href="{{ route('roles.index') }}" class="btn btn-secondary ml-2">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                    <div class="col-md-6 text-right">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> All fields marked with * are required
                        </small>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop

@section('css')
    <style>
        .required::after {
            content: " *";
            color: red;
        }
        .card-header h6 {
            font-weight: bold;
        }
        .form-check {
            margin-bottom: 0.5rem;
        }
    </style>
@stop

@section('js')
    <script>
        function selectAllPermissions() {
            $('input[name="permissions[]"]').prop('checked', true);
        }

        function deselectAllPermissions() {
            $('input[name="permissions[]"]').prop('checked', false);
        }

        // Form validation
        $('form').on('submit', function(e) {
            let roleName = $('#name').val().trim();
            if (roleName === '') {
                e.preventDefault();
                alert('Please enter a role name.');
                $('#name').focus();
                return false;
            }
        });
    </script>
@stop
