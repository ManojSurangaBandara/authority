<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bus_escort_assignments', function (Blueprint $table) {
            // Add route type field
            $table->enum('route_type', ['living_out', 'living_in'])->default('living_out')->after('bus_route_id');

            // Make bus_route_id nullable to support living_in routes
            $table->foreignId('bus_route_id')->nullable()->change();

            // Add living_in_bus_id for living in routes
            $table->foreignId('living_in_bus_id')->nullable()->constrained('living_in_buses')->onDelete('cascade')->after('route_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bus_escort_assignments', function (Blueprint $table) {
            $table->dropForeign(['living_in_bus_id']);
            $table->dropColumn(['route_type', 'living_in_bus_id']);
        });
    }
};
