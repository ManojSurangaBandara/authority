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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('escort_id')->constrained('users');
            $table->unsignedBigInteger('bus_route_id');
            $table->enum('route_type', ['living_in', 'living_out']);
            $table->foreignId('driver_id')->constrained('users');
            $table->foreignId('bus_id')->constrained('buses');
            $table->foreignId('slcmp_incharge_id')->nullable()->constrained('users');
            $table->timestamp('trip_start_time');
            $table->timestamp('trip_end_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
