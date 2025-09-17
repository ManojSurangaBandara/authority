<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
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
            echo "Admin user not found. Creating admin user...\n";
            
            // Create admin user if doesn't exist
            $adminUser = User::create([
                'name' => 'System Administrator',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('password'), // Change this in production
                'is_active' => 1,
                'email_verified_at' => now(),
            ]);
            
            echo "Admin user created successfully.\n";
        }
        
        // Find the System Administrator role
        $systemAdminRole = Role::where('name', 'System Administrator (DMOV)')->first();
        
        if (!$systemAdminRole) {
            echo "System Administrator role not found. Please run SystemAdministratorSeeder first.\n";
            return;
        }
        
        // Remove any existing roles from admin user
        $adminUser->roles()->detach();
        
        // Assign System Administrator role
        $adminUser->assignRole($systemAdminRole);
        
        echo "System Administrator role assigned to admin user successfully.\n";
        echo "Admin User: {$adminUser->name} ({$adminUser->email})\n";
        echo "Role: {$systemAdminRole->name}\n";
        echo "Total permissions: " . $adminUser->getAllPermissions()->count() . "\n";
    }
}
