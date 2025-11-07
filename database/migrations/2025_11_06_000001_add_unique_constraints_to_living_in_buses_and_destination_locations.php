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
        Schema::table('living_in_buses', function (Blueprint $table) {
            $table->unique('name');
        });

        Schema::table('destination_locations', function (Blueprint $table) {
            $table->unique('destination_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('living_in_buses', function (Blueprint $table) {
            $table->dropUnique(['name']);
        });

        Schema::table('destination_locations', function (Blueprint $table) {
            $table->dropUnique(['destination_location']);
        });
    }
};
