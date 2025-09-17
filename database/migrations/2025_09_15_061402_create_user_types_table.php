<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique(); // Unique code for each user type
            $table->string('name', 100); // Display name
            $table->string('category', 50); // 'branch' or 'movement'
            $table->text('description')->nullable();
            $table->json('permissions')->nullable(); // Store specific permissions as JSON
            $table->integer('hierarchy_level')->default(0); // For approval workflow order
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert the 9 user types from specification
        DB::table('user_types')->insert([
            [
                'code' => 'bus_pass_subject_clerk_branch',
                'name' => 'Bus Pass Subject Clerk',
                'category' => 'branch',
                'description' => 'Subject Clerk responsible for creating bus pass applications at Branch/Directorate level',
                'hierarchy_level' => 1,
                'permissions' => json_encode([
                    'create_bus_pass_application',
                    'request_reactivate_bus_pass',
                    'view_branch_reports',
                    'view_notifications'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'staff_officer_branch',
                'name' => 'Staff Officer',
                'category' => 'branch',
                'description' => 'Staff Officer who recommends bus pass applications at Branch/Directorate level',
                'hierarchy_level' => 2,
                'permissions' => json_encode([
                    'recommend_bus_pass_application',
                    'recommend_reactivate_bus_pass',
                    'view_branch_reports',
                    'view_notifications'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'director_branch',
                'name' => 'Director',
                'category' => 'branch',
                'description' => 'Director who approves bus pass applications at Branch/Directorate level',
                'hierarchy_level' => 3,
                'permissions' => json_encode([
                    'approve_bus_pass_application',
                    'view_branch_reports',
                    'view_notifications'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'subject_clerk_movement',
                'name' => 'Subject Clerk (Movement)',
                'category' => 'movement',
                'description' => 'Subject Clerk at Directorate of Movement for bus pass processing',
                'hierarchy_level' => 4,
                'permissions' => json_encode([
                    'edit_bus_pass_application',
                    'integrate_branch_card',
                    'print_temporary_bus_pass',
                    'assign_bus_personnel',
                    'allocate_fuel',
                    'view_movement_reports'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'staff_officer_2_movement',
                'name' => 'Staff Officer 2 (Bus)',
                'category' => 'movement',
                'description' => 'Staff Officer 2 responsible for bus operations at Directorate of Movement',
                'hierarchy_level' => 5,
                'permissions' => json_encode([
                    'recommend_branch_card_integration',
                    'approve_reactivate_bus_pass',
                    'approve_bus_personnel_assignments',
                    'approve_fuel_allocation',
                    'view_bus_notifications',
                    'view_movement_reports'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'staff_officer_1_movement',
                'name' => 'Staff Officer 1',
                'category' => 'movement',
                'description' => 'Staff Officer 1 at Directorate of Movement',
                'hierarchy_level' => 6,
                'permissions' => json_encode([
                    'recommend_branch_card_integration_final',
                    'view_bus_notifications',
                    'view_movement_reports'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'col_mov_movement',
                'name' => 'Colonel Movement',
                'category' => 'movement',
                'description' => 'Colonel Movement at Directorate of Movement',
                'hierarchy_level' => 7,
                'permissions' => json_encode([
                    'recommend_final_approval',
                    'view_bus_notifications',
                    'view_movement_reports'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'director_movement',
                'name' => 'Director (Movement)',
                'category' => 'movement',
                'description' => 'Director at Directorate of Movement - final approval authority',
                'hierarchy_level' => 8,
                'permissions' => json_encode([
                    'final_approve_branch_card_integration',
                    'approve_temporary_bus_pass_printing',
                    'view_bus_notifications',
                    'view_movement_reports'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'bus_escort_movement',
                'name' => 'Bus Escort',
                'category' => 'movement',
                'description' => 'Bus Escort responsible for mobile operations and QR scanning',
                'hierarchy_level' => 0, // No hierarchy level for operational role
                'permissions' => json_encode([
                    'scan_qr_codes',
                    'mark_bus_timings',
                    'report_bus_incidents',
                    'mobile_app_access'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_types');
    }
};
