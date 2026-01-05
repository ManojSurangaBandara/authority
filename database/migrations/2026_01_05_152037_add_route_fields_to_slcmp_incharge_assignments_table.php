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
        Schema::table('slcmp_incharge_assignments', function (Blueprint $table) {
            $table->unsignedBigInteger('route_id')->nullable()->after('bus_route_id');
            $table->enum('route_type', ['living_out', 'living_in'])->default('living_out')->after('route_id');
            $table->unsignedBigInteger('living_in_bus_id')->nullable()->after('route_type');

            $table->foreign('living_in_bus_id')->references('id')->on('living_in_buses')->onDelete('cascade');

            $table->index(['route_type', 'route_id']);
        });

        // Migrate existing data
        DB::statement("
            UPDATE slcmp_incharge_assignments
            SET route_id = bus_route_id, route_type = 'living_out'
            WHERE bus_route_id IS NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('slcmp_incharge_assignments', function (Blueprint $table) {
            $table->dropForeign(['living_in_bus_id']);
            $table->dropIndex(['route_type', 'route_id']);
            $table->dropColumn(['route_id', 'route_type', 'living_in_bus_id']);
        });
    }
};
