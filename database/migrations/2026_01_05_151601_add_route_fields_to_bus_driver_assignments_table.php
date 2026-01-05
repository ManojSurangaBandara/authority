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
        Schema::table('bus_driver_assignments', function (Blueprint $table) {
            $table->unsignedBigInteger('route_id')->nullable()->after('bus_route_id');
            $table->enum('route_type', ['living_out', 'living_in'])->nullable()->after('route_id');
            $table->unsignedBigInteger('living_in_bus_id')->nullable()->after('route_type');

            // Add foreign key constraints
            $table->foreign('living_in_bus_id')->references('id')->on('living_in_buses')->onDelete('cascade');

            // Add indexes
            $table->index(['route_id', 'route_type']);
        });

        // Migrate existing data
        DB::statement("
            UPDATE bus_driver_assignments
            SET route_id = bus_route_id,
                route_type = 'living_out'
            WHERE route_id IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bus_driver_assignments', function (Blueprint $table) {
            $table->dropForeign(['living_in_bus_id']);
            $table->dropIndex(['route_id', 'route_type']);
            $table->dropColumn(['route_id', 'route_type', 'living_in_bus_id']);
        });
    }
};
