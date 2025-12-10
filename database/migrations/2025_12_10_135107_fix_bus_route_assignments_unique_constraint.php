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
        // The constraint was already fixed in the original migration
        // Nothing to do here
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bus_route_assignments', function (Blueprint $table) {
            // Restore the original constraint
            $table->unique(['bus_id', 'status'], 'unique_active_bus_assignment');
        });
    }
};
