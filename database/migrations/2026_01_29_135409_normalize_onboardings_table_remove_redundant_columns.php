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
        Schema::table('onboardings', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['escort_id']);
            $table->dropForeign(['bus_route_id']);
            $table->dropForeign(['living_in_bus_id']);

            // Drop redundant columns
            $table->dropColumn([
                'escort_id',
                'bus_route_id',
                'living_in_bus_id',
                'route_type',
                'branch_card_id',
                'serial_number',
                'boarding_data'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('onboardings', function (Blueprint $table) {
            // Add back the columns
            $table->unsignedBigInteger('escort_id')->after('bus_pass_application_id');
            $table->unsignedBigInteger('bus_route_id')->nullable()->after('escort_id');
            $table->unsignedBigInteger('living_in_bus_id')->nullable()->after('bus_route_id');
            $table->string('route_type')->after('living_in_bus_id');
            $table->string('branch_card_id')->after('route_type');
            $table->string('serial_number')->nullable()->after('branch_card_id');
            $table->json('boarding_data')->nullable()->after('serial_number');

            // Add back foreign keys
            $table->foreign('escort_id')->references('id')->on('escorts')->onDelete('cascade');
            $table->foreign('bus_route_id')->references('id')->on('bus_routes')->onDelete('set null');
            $table->foreign('living_in_bus_id')->references('id')->on('living_in_buses')->onDelete('set null');
        });
    }
};
