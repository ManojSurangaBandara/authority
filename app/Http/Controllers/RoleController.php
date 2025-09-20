<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:System Administrator (DMOV)']);
    }

    /**
     * Display a listing of roles
     */
    public function index()
    {
        $roles = Role::with('permissions')->paginate(15);
        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role
     */
    public function create()
    {
        $permissions = Permission::all()->groupBy(function($permission) {
            // Group permissions by category based on naming convention
            $parts = explode('_', $permission->name);
            return ucfirst($parts[count($parts) - 1] ?? 'General');
        });
        
        return view('roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $request->name,
        ]);

        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            $role->givePermissionTo($permissions);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully with assigned permissions.');
    }

    /**
     * Display the specified role
     */
    public function show($id)
    {
        $role = Role::with('permissions', 'users')->findOrFail($id);
        return view('roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified role
     */
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all()->groupBy(function($permission) {
            // Group permissions by category based on naming convention
            $parts = explode('_', $permission->name);
            return ucfirst($parts[count($parts) - 1] ?? 'General');
        });
        
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        
        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Note: Role name updates are disabled for system integrity
        // Only permissions can be updated

        // Sync permissions
        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            $role->syncPermissions($permissions);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role permissions updated successfully.');
    }

    /**
     * Remove the specified role
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        
        // Define system-critical roles that cannot be deleted
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
        
        // Prevent deleting system-critical roles
        if (in_array($role->name, $systemRoles)) {
            return redirect()->route('roles.index')
                ->with('error', 'Cannot delete system role "' . $role->name . '". This role is required for the approval workflow.');
        }

        // Check if role has users assigned
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', 'Cannot delete role "' . $role->name . '" because it has ' . $role->users()->count() . ' user(s) assigned. Please reassign users first.');
        }

        $roleName = $role->name;
        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Custom role "' . $roleName . '" has been deleted successfully.');
    }

    /**
     * Show permissions management for a role
     */
    public function permissions($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all()->groupBy(function($permission) {
            // Group permissions by category
            $parts = explode('_', $permission->name);
            return ucfirst($parts[count($parts) - 1] ?? 'General');
        });
        
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        
        return view('roles.permissions', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update permissions for a role
     */
    public function updatePermissions(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            $role->syncPermissions($permissions);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('roles.show', $role)
            ->with('success', 'Role permissions updated successfully.');
    }

    /**
     * Get role hierarchy levels for approval workflow
     */
    public function hierarchy()
    {
        $hierarchyRoles = [
            1 => 'Bus Pass Subject Clerk (Branch)',
            2 => 'Staff Officer (Branch)',
            3 => 'Director (Branch)',
            4 => 'Subject Clerk (DMOV)',
            5 => 'Staff Officer 2 (DMOV)',
            6 => 'Staff Officer 1 (DMOV)',
            7 => 'Col Mov (DMOV)',
            8 => 'Director (DMOV)',
            9 => 'Bus Escort (DMOV)',
            10 => 'System Administrator (DMOV)',
        ];

        $roles = Role::whereIn('name', array_values($hierarchyRoles))->get();
        
        return view('roles.hierarchy', compact('roles', 'hierarchyRoles'));
    }
}
