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
        // Insert the new clearance status
        DB::table('bus_pass_statuses')->insert([
            'code' => 'clearance',
            'label' => 'Cleared',
            'badge_color' => 'warning',
            'description' => 'Bus pass cleared for person',
            'sort_order' => 17,
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
        // Remove the clearance status
        DB::table('bus_pass_statuses')
            ->where('code', 'clearance')
            ->delete();
    }
};
