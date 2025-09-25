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
        Schema::table('bus_routes', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['bus_id']);

            // Make bus_id nullable
            $table->unsignedBigInteger('bus_id')->nullable()->change();

            // Add foreign key constraint back but allow nulls
            $table->foreign('bus_id')->references('id')->on('buses')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bus_routes', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['bus_id']);

            // Make bus_id not nullable
            $table->unsignedBigInteger('bus_id')->nullable(false)->change();

            // Add foreign key constraint back
            $table->foreign('bus_id')->references('id')->on('buses')->onDelete('cascade');
        });
    }
};
