<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SystemAdministratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates System Administrator role as per ASDF-11 specification
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create System Administrator permissions as per ASDF-11 Section 4.3.1
        $systemAdminPermissions = [
            // User Management
            'add_new_user_accounts',
            'reset_others_password',
            'manage_user_accounts',
            
            // API Access
            'access_branches_directorates_api',
            'access_mobile_application_scan',
            
            // Bus Route Management
            'add_bus_routes',
            'edit_bus_routes',
            'delete_bus_routes',
            'manage_bus_routes',
            
            // Assignment Management
            'assign_bus_driver',
            'assign_bus_escort',
            'assign_slcmp_incharge',
            'assign_bus_filling_stations',
            
            // System Administration
            'system_admin_access',
            'full_system_reports',
            'system_configuration',
            'mobile_app_management',
        ];

        // Create permissions if they don't exist
        foreach ($systemAdminPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create System Administrator role as per ASDF-11
        $systemAdmin = Role::firstOrCreate(['name' => 'System Administrator (DMOV)']);
        
        // Assign all system admin permissions
        $systemAdmin->givePermissionTo($systemAdminPermissions);
        
        // Also give all existing permissions for full system access
        $allPermissions = Permission::all();
        $systemAdmin->givePermissionTo($allPermissions);

        echo "System Administrator role created successfully with " . count($systemAdminPermissions) . " specific permissions.\n";
        echo "Total permissions assigned: " . $systemAdmin->permissions()->count() . "\n";
    }
}
