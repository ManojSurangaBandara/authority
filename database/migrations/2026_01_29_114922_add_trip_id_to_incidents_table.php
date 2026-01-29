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
        Schema::table('incidents', function (Blueprint $table) {
            // Add trip_id for normalization
            $table->unsignedBigInteger('trip_id')->nullable()->after('incident_type_id');
            $table->foreign('trip_id')->references('id')->on('trips')->onDelete('set null');
            $table->index('trip_id');

            // Drop redundant columns since they can be derived from trip
            $table->dropForeign(['escort_id']);
            $table->dropForeign(['slcmp_incharge_id']);
            $table->dropForeign(['driver_id']);
            $table->dropForeign(['bus_id']);
            $table->dropColumn(['escort_id', 'bus_route_id', 'route_type', 'slcmp_incharge_id', 'driver_id', 'bus_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropForeign(['trip_id']);
            $table->dropIndex(['trip_id']);
            $table->dropColumn('trip_id');

            // Restore the dropped columns
            $table->unsignedBigInteger('escort_id')->nullable();
            $table->foreign('escort_id')->references('id')->on('escorts');

            $table->unsignedBigInteger('bus_route_id')->nullable();
            // Foreign key will be handled in application logic

            $table->enum('route_type', ['living_in', 'living_out'])->nullable();

            $table->unsignedBigInteger('slcmp_incharge_id')->nullable();
            $table->foreign('slcmp_incharge_id')->references('id')->on('slcmp_incharges');

            $table->unsignedBigInteger('driver_id')->nullable();
            $table->foreign('driver_id')->references('id')->on('drivers');

            $table->unsignedBigInteger('bus_id')->nullable();
            $table->foreign('bus_id')->references('id')->on('buses');
        });
    }
};
