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
        Schema::create('bus_pass_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique(); // e.g., 'pending', 'approved'
            $table->string('label', 100); // e.g., 'Pending', 'Approved by Staff Officer'
            $table->string('badge_color', 20)->default('secondary'); // Bootstrap badge color
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default statuses based on workflow document
        DB::table('bus_pass_statuses')->insert([
            [
                'code' => 'pending_subject_clerk',
                'label' => 'Pending - Subject Clerk Review',
                'badge_color' => 'warning',
                'description' => 'Application submitted, pending subject clerk review',
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'pending_staff_officer_branch',
                'label' => 'Pending - Staff Officer (Branch/Dte)',
                'badge_color' => 'info',
                'description' => 'Application forwarded to Staff Officer at Branch/Directorate',
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'pending_director_branch',
                'label' => 'Pending - Director (Branch/Dte)',
                'badge_color' => 'primary',
                'description' => 'Application pending Director approval at Branch/Directorate',
                'sort_order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'forwarded_to_movement',
                'label' => 'Forwarded to Movement',
                'badge_color' => 'secondary',
                'description' => 'Application forwarded to Directorate of Movement',
                'sort_order' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'pending_staff_officer_2_mov',
                'label' => 'Pending - Staff Officer 2 (Movement)',
                'badge_color' => 'info',
                'description' => 'Application pending Staff Officer 2 review at Movement',
                'sort_order' => 5,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'pending_staff_officer_1_mov',
                'label' => 'Pending - Staff Officer 1 (Movement)',
                'badge_color' => 'info',
                'description' => 'Application pending Staff Officer 1 review at Movement',
                'sort_order' => 6,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'pending_col_mov',
                'label' => 'Pending - Colonel Movement',
                'badge_color' => 'primary',
                'description' => 'Application pending Colonel Movement approval',
                'sort_order' => 7,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'pending_director_mov',
                'label' => 'Pending - Director (Movement)',
                'badge_color' => 'primary',
                'description' => 'Application pending Director approval at Movement',
                'sort_order' => 8,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'approved_for_integration',
                'label' => 'Approved for Branch Card Integration',
                'badge_color' => 'success',
                'description' => 'Approved for integration into branch card',
                'sort_order' => 9,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'approved_for_temp_card',
                'label' => 'Approved for Temporary Card',
                'badge_color' => 'success',
                'description' => 'Approved for temporary bus pass card printing',
                'sort_order' => 10,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'integrated_to_branch_card',
                'label' => 'Integrated to Branch Card',
                'badge_color' => 'success',
                'description' => 'Bus pass details integrated to branch card',
                'sort_order' => 11,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'temp_card_printed',
                'label' => 'Temporary Card Printed',
                'badge_color' => 'success',
                'description' => 'Temporary bus pass card printed',
                'sort_order' => 12,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'temp_card_handed_over',
                'label' => 'Temporary Card Handed Over',
                'badge_color' => 'success',
                'description' => 'Temporary bus pass card handed over to branch/directorate',
                'sort_order' => 13,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'rejected',
                'label' => 'Rejected',
                'badge_color' => 'danger',
                'description' => 'Application rejected',
                'sort_order' => 14,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'deactivated',
                'label' => 'Deactivated',
                'badge_color' => 'dark',
                'description' => 'Bus pass deactivated (not used for 3+ months)',
                'sort_order' => 15,
                'is_active' => true,
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
        Schema::dropIfExists('bus_pass_statuses');
    }
};
