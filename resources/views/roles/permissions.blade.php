@extends('adminlte::page')

@section('title', 'Manage Permissions - ' . $role->name)

@section('content_header')
    <div class="row">
        <div class="col-md-6">
            <h1><i class="fas fa-key"></i> Manage Permissions</h1>
            <p class="text-muted">Role: <strong>{{ $role->name }}</strong></p>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('roles.show', $role) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> View Role Details
            </a>
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Roles
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

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title">
                <i class="fas fa-shield-alt"></i> Permission Assignment for: {{ $role->name }}
            </h3>
        </div>
        
        <form action="{{ route('roles.update-permissions', $role) }}" method="POST">
            @csrf
            @method('PATCH')
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

                <!-- Permission Statistics -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-key"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Permissions</span>
                                <span class="info-box-number" id="totalPermissions">
                                    {{ $permissions->flatten()->count() }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-success">
                            <span class="info-box-icon"><i class="fas fa-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Assigned</span>
                                <span class="info-box-number" id="assignedCount">
                                    {{ count($rolePermissions) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-warning">
                            <span class="info-box-icon"><i class="fas fa-minus"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Not Assigned</span>
                                <span class="info-box-number" id="notAssignedCount">
                                    {{ $permissions->flatten()->count() - count($rolePermissions) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-secondary">
                            <span class="info-box-icon"><i class="fas fa-folder"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Categories</span>
                                <span class="info-box-number">{{ $permissions->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bulk Actions -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-secondary">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-tools"></i> Bulk Actions</h6>
                            </div>
                            <div class="card-body py-2">
                                <button type="button" class="btn btn-sm btn-success" onclick="selectAllPermissions()">
                                    <i class="fas fa-check-square"></i> Select All
                                </button>
                                <button type="button" class="btn btn-sm btn-warning" onclick="deselectAllPermissions()">
                                    <i class="fas fa-square"></i> Deselect All
                                </button>
                                <button type="button" class="btn btn-sm btn-info" onclick="toggleAllPermissions()">
                                    <i class="fas fa-exchange-alt"></i> Toggle All
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permissions by Category -->
                @if($permissions->isNotEmpty())
                    <div class="row">
                        @foreach($permissions as $category => $categoryPermissions)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card {{ in_array($category, ['Admin', 'Management']) ? 'border-danger' : 'border-info' }}">
                                    <div class="card-header {{ in_array($category, ['Admin', 'Management']) ? 'bg-danger' : 'bg-info' }} text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-folder"></i> {{ $category }} Permissions
                                            <span class="badge badge-light ml-2">{{ $categoryPermissions->count() }}</span>
                                        </h6>
                                        <div class="card-tools">
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-light" 
                                                    onclick="toggleCategory('{{ $category }}')">
                                                <i class="fas fa-exchange-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        @foreach($categoryPermissions as $permission)
                                            <div class="form-check mb-2">
                                                <input type="checkbox" 
                                                       class="form-check-input permission-checkbox category-{{ $category }}" 
                                                       id="permission_{{ $permission->id }}" 
                                                       name="permissions[]" 
                                                       value="{{ $permission->id }}"
                                                       {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}
                                                       onchange="updateCounts()">
                                                <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                    <strong>{{ ucfirst(str_replace('_', ' ', $permission->name)) }}</strong>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No permissions available. 
                        Please ensure permissions are seeded in the database.
                    </div>
                @endif
            </div>

            <div class="card-footer">
                <div class="row">
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-save"></i> Update Permissions
                        </button>
                        <a href="{{ route('roles.show', $role) }}" class="btn btn-secondary ml-2">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                    <div class="col-md-6 text-right">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> Changes will be applied to all users with this role
                        </small>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Role Usage Information -->
    @if($role->users->count() > 0)
        <div class="card mt-3">
            <div class="card-header bg-warning">
                <h5 class="mb-0">
                    <i class="fas fa-users"></i> Impact Assessment
                </h5>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <strong>{{ $role->users->count() }}</strong> user(s) will be affected by permission changes:
                </p>
                <div class="row">
                    @foreach($role->users->chunk(ceil($role->users->count() / 3)) as $userChunk)
                        <div class="col-md-4">
                            @foreach($userChunk as $user)
                                <div class="mb-1">
                                    <i class="fas fa-user"></i> 
                                    <strong>{{ $user->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
@stop

@section('css')
    <style>
        .form-check {
            padding-left: 1.5rem;
        }
        .card-header h6 {
            font-weight: bold;
        }
        .info-box-number {
            font-size: 1.5rem;
            font-weight: bold;
        }
    </style>
@stop

@section('js')
    <script>
        function selectAllPermissions() {
            $('.permission-checkbox').prop('checked', true);
            updateCounts();
        }

        function deselectAllPermissions() {
            $('.permission-checkbox').prop('checked', false);
            updateCounts();
        }

        function toggleAllPermissions() {
            $('.permission-checkbox').each(function() {
                $(this).prop('checked', !$(this).prop('checked'));
            });
            updateCounts();
        }

        function toggleCategory(category) {
            $('.category-' + category).each(function() {
                $(this).prop('checked', !$(this).prop('checked'));
            });
            updateCounts();
        }

        function updateCounts() {
            let totalChecked = $('.permission-checkbox:checked').length;
            let totalPermissions = $('.permission-checkbox').length;
            let notAssigned = totalPermissions - totalChecked;
            
            $('#assignedCount').text(totalChecked);
            $('#notAssignedCount').text(notAssigned);
        }

        // Initialize counts on page load
        $(document).ready(function() {
            updateCounts();
        });

        // Confirm form submission
        $('form').on('submit', function(e) {
            let checkedCount = $('.permission-checkbox:checked').length;
            let message = `Are you sure you want to update permissions?\n\n`;
            message += `This will assign ${checkedCount} permissions to the "${{{ $role->name }}}" role.\n`;
            message += `This change will affect {{ $role->users->count() }} user(s).`;
            
            if (!confirm(message)) {
                e.preventDefault();
                return false;
            }
        });
    </script>
@stop
