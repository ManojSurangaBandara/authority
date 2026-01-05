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
        Schema::table('bus_driver_assignments', function (Blueprint $table) {
            $table->unsignedBigInteger('bus_route_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('bus_driver_assignments', function (Blueprint $table) {
            $table->unsignedBigInteger('bus_route_id')->nullable(false)->change();
        });
    }
};
