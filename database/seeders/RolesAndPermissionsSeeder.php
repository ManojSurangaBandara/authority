<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Bus pass management
            'create_bus_pass',
            'edit_bus_pass',
            'view_bus_pass',
            'delete_bus_pass',
            'approve_bus_pass',
            'reject_bus_pass',

            // Bus management
            'manage_buses',
            'assign_drivers',
            'assign_escorts',
            'view_bus_assignments',

            // User management
            'manage_users',
            'view_users',

            // Reports
            'view_reports',
            'generate_reports',

            // Admin functions
            'manage_establishments',
            'system_admin',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles with specific permissions
        $this->createBranchRoles();
        $this->createMovementRoles();
    }

    private function createBranchRoles()
    {
        // Branch - Bus Pass Subject Clerk
        $branchClerk = Role::create(['name' => 'Bus Pass Subject Clerk (Branch)']);
        $branchClerk->givePermissionTo([
            'create_bus_pass',
            'edit_bus_pass',
            'view_bus_pass',
            'approve_bus_pass',
            'view_reports'
        ]);

        // Branch - Staff Officer
        $branchStaffOfficer = Role::create(['name' => 'Staff Officer (Branch)']);
        $branchStaffOfficer->givePermissionTo([
            'create_bus_pass',
            'edit_bus_pass',
            'view_bus_pass',
            'approve_bus_pass',
            'reject_bus_pass',
            'view_reports'
        ]);

        // Branch - Director
        $branchDirector = Role::create(['name' => 'Director (Branch)']);
        $branchDirector->givePermissionTo([
            'create_bus_pass',
            'edit_bus_pass',
            'view_bus_pass',
            'approve_bus_pass',
            'reject_bus_pass',
            'view_reports',
            'generate_reports',
            'manage_users'
        ]);
    }

    private function createMovementRoles()
    {
        // DMOV - Subject Clerk
        $movementClerk = Role::create(['name' => 'Subject Clerk (DMOV)']);
        $movementClerk->givePermissionTo([
            'view_bus_pass',
            'edit_bus_pass',
            'manage_buses',
            'assign_drivers',
            'assign_escorts',
            'view_bus_assignments'
        ]);

        // DMOV - Staff Officer 2
        $movementStaffOfficer2 = Role::create(['name' => 'Staff Officer 2 (DMOV)']);
        $movementStaffOfficer2->givePermissionTo([
            'view_bus_pass',
            'edit_bus_pass',
            'approve_bus_pass',
            'reject_bus_pass',
            'manage_buses',
            'assign_drivers',
            'assign_escorts',
            'view_bus_assignments'
        ]);

        // DMOV - Staff Officer 1
        $movementStaffOfficer1 = Role::create(['name' => 'Staff Officer 1 (DMOV)']);
        $movementStaffOfficer1->givePermissionTo([
            'view_bus_pass',
            'edit_bus_pass',
            'approve_bus_pass',
            'reject_bus_pass',
            'manage_buses',
            'assign_drivers',
            'assign_escorts',
            'view_bus_assignments',
            'view_reports'
        ]);

        // DMOV - Col Mov
        $colMov = Role::create(['name' => 'Col Mov (DMOV)']);
        $colMov->givePermissionTo([
            'view_bus_pass',
            'edit_bus_pass',
            'approve_bus_pass',
            'reject_bus_pass',
            'manage_buses',
            'assign_drivers',
            'assign_escorts',
            'view_bus_assignments',
            'view_reports',
            'generate_reports'
        ]);

        // DMOV - Director
        $movementDirector = Role::create(['name' => 'Director (DMOV)']);
        $movementDirector->givePermissionTo([
            'view_bus_pass',
            'edit_bus_pass',
            'approve_bus_pass',
            'reject_bus_pass',
            'manage_buses',
            'assign_drivers',
            'assign_escorts',
            'view_bus_assignments',
            'view_reports',
            'generate_reports',
            'manage_users',
            'system_admin'
        ]);

        // DMOV - Bus Escort
        $busEscort = Role::create(['name' => 'Bus Escort (DMOV)']);
        $busEscort->givePermissionTo([
            'view_bus_pass',
            'view_bus_assignments'
        ]);
    }
}
