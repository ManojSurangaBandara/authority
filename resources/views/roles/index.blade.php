@extends('adminlte::page')

@section('title', 'Role Management')

@section('content_header')
    <div class="row">
        <div class="col-md-6">
            <h1><i class="fas fa-user-shield"></i> Role Management</h1>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('roles.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create New Role
            </a>
            <a href="{{ route('roles.hierarchy') }}" class="btn btn-info">
                <i class="fas fa-sitemap"></i> View Hierarchy
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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-list"></i> All Roles</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="rolesTable">
                    <thead class="table">
                        <tr>
                            <th>#</th>
                            <th>Role Name</th>
                            <th>Permissions Count</th>
                            <th>Users Count</th>
                            <th>Created Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $role->name }}</strong>
                                    @if($role->name === 'System Administrator (DMOV)')
                                        <span class="badge badge-danger ml-1">Super Admin</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ $role->permissions->count() }} permissions
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-secondary">
                                        {{ $role->users->count() }} users
                                    </span>
                                </td>
                                <td>{{ $role->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('roles.show', $role) }}"
                                           class="btn btn-sm btn-info"
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('roles.permissions', $role) }}"
                                           class="btn btn-sm btn-warning"
                                           title="Manage Permissions">
                                            <i class="fas fa-key"></i>
                                        </a>
                                        @php
                                            $systemRoles = [
                                                'System Administrator (DMOV)',
                                                'Bus Pass Subject Clerk (Branch)',
                                                'Staff Officer (Branch)',
                                                'Director (Branch)',
                                                'Subject Clerk (DMOV)',
                                                'Staff Officer 2 (DMOV)',
                                                'Staff Officer 1 (DMOV)',
                                                'Col Mov (DMOV)',
                                                'Director (DMOV)',
                                                'Bus Escort (DMOV)'
                                            ];
                                            $isSystemRole = in_array($role->name, $systemRoles);
                                            $hasUsers = $role->users->count() > 0;
                                        @endphp
                                        @if(!$isSystemRole)
                                            @if($hasUsers)
                                                <button type="button"
                                                        class="btn btn-sm btn-secondary"
                                                        title="Cannot delete: {{ $role->users->count() }} user(s) assigned"
                                                        disabled>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @else
                                                <button type="button"
                                                        class="btn btn-sm btn-danger"
                                                        title="Delete Custom Role"
                                                        onclick="confirmDelete({{ $role->id }}, '{{ $role->name }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        @else
                                            <button type="button"
                                                    class="btn btn-sm btn-secondary"
                                                    title="System role cannot be deleted"
                                                    disabled>
                                                <i class="fas fa-lock"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    <i class="fas fa-inbox"></i> No roles found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $roles->links() }}
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle"></i> Confirm Delete
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the role <strong id="roleName"></strong>?</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Warning:</strong> This action cannot be undone. This will permanently delete the custom role.
                    </div>
                    <p class="text-muted">
                        <small><i class="fas fa-info-circle"></i> System roles are protected and cannot be deleted.</small>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete Role
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
@stop

@section('js')
    <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#rolesTable').DataTable({
                responsive: true,
                order: [[1, 'asc']],
                pageLength: 10,
                language: {
                    search: "Search roles:",
                    lengthMenu: "Show _MENU_ roles per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ roles",
                    infoEmpty: "No roles available",
                    infoFiltered: "(filtered from _MAX_ total roles)"
                }
            });
        });

        function confirmDelete(roleId, roleName) {
            $('#roleName').text(roleName);
            $('#deleteForm').attr('action', '/roles/' + roleId);
            $('#deleteModal').modal('show');
        }
    </script>
@stop
