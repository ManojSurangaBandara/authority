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
        Schema::table('bus_filling_station_assignments', function (Blueprint $table) {
            $table->dropColumn(['assigned_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bus_filling_station_assignments', function (Blueprint $table) {
            $table->date('assigned_date');
            $table->date('end_date')->nullable();
        });
    }
};
