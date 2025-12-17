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
        // Update sort_order for statuses after integrated_to_branch_card to make room
        DB::table('bus_pass_statuses')
            ->where('sort_order', '>=', 12)
            ->increment('sort_order');

        // Insert the new integrated_to_temp_card status
        DB::table('bus_pass_statuses')->insert([
            'code' => 'integrated_to_temp_card',
            'label' => 'Integrated to Temporary Card',
            'badge_color' => 'success',
            'description' => 'Bus pass details integrated to temporary card system',
            'sort_order' => 12,
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
        // Remove the integrated_to_temp_card status
        DB::table('bus_pass_statuses')
            ->where('code', 'integrated_to_temp_card')
            ->delete();

        // Revert sort_order changes
        DB::table('bus_pass_statuses')
            ->where('sort_order', '>', 12)
            ->decrement('sort_order');
    }
};
