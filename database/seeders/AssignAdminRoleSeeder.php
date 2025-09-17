<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AssignAdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Assigns System Administrator role to the admin user
     */
    public function run(): void
    {
        // Find the admin user by email
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        
        if (!$adminUser) {
            echo "Admin user not found with email: admin@gmail.com\n";
            return;
        }

        // Find the System Administrator role
        $systemAdminRole = Role::where('name', 'System Administrator (DMOV)')->first();
        
        if (!$systemAdminRole) {
            echo "System Administrator role not found. Please run SystemAdministratorSeeder first.\n";
            return;
        }

        // Assign the role to admin user
        $adminUser->assignRole($systemAdminRole);
        
        echo "Successfully assigned 'System Administrator (DMOV)' role to user: {$adminUser->name} ({$adminUser->email})\n";
        echo "User now has roles: " . $adminUser->roles->pluck('name')->implode(', ') . "\n";
    }
}
