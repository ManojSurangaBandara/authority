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
        // Update sort_order for statuses after approved_for_temp_card to make room
        DB::table('bus_pass_statuses')
            ->where('sort_order', '>=', 11)
            ->increment('sort_order');

        // Insert the new rejected_for_integration status
        DB::table('bus_pass_statuses')->insert([
            'code' => 'rejected_for_integration',
            'label' => 'Rejected for Integration',
            'badge_color' => 'warning',
            'description' => 'Application rejected during integration process and forwarded back for review',
            'sort_order' => 11,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the rejected_for_integration status
        DB::table('bus_pass_statuses')
            ->where('code', 'rejected_for_integration')
            ->delete();

        // Revert sort_order changes
        DB::table('bus_pass_statuses')
            ->where('sort_order', '>', 11)
            ->decrement('sort_order');
    }
};
