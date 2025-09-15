<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UpdateRoleNamesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update role names from Movement to DMOV
        $rolesToUpdate = [
            'Subject Clerk (Movement)' => 'Subject Clerk (DMOV)',
            'Staff Officer 2 (Movement)' => 'Staff Officer 2 (DMOV)',
            'Staff Officer 1 (Movement)' => 'Staff Officer 1 (DMOV)',
            'Col Mov (Movement)' => 'Col Mov (DMOV)',
            'Director (Movement)' => 'Director (DMOV)',
            'Bus Escort (Movement)' => 'Bus Escort (DMOV)',
        ];

        foreach ($rolesToUpdate as $oldName => $newName) {
            $role = Role::where('name', $oldName)->first();
            if ($role) {
                $role->update(['name' => $newName]);
                echo "Updated role: {$oldName} -> {$newName}\n";
            }
        }
    }
}
