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
        Schema::table('trips', function (Blueprint $table) {
            // Drop existing foreign keys
            $table->dropForeign(['escort_id']);
            $table->dropForeign(['driver_id']);
            $table->dropForeign(['slcmp_incharge_id']);

            // Add correct foreign keys
            $table->foreign('escort_id')->references('id')->on('escorts');
            $table->foreign('driver_id')->references('id')->on('drivers');
            $table->foreign('slcmp_incharge_id')->references('id')->on('slcmp_incharges');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            // Drop the corrected foreign keys
            $table->dropForeign(['escort_id']);
            $table->dropForeign(['driver_id']);
            $table->dropForeign(['slcmp_incharge_id']);

            // Restore original foreign keys (pointing to users table)
            $table->foreign('escort_id')->references('id')->on('users');
            $table->foreign('driver_id')->references('id')->on('users');
            $table->foreign('slcmp_incharge_id')->references('id')->on('users');
        });
    }
};
