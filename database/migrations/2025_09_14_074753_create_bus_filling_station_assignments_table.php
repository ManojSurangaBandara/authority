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
        Schema::create('bus_filling_station_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_id')->constrained('buses')->onDelete('cascade');
            $table->foreignId('filling_station_id')->constrained('filling_stations')->onDelete('cascade');
            $table->date('assigned_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('created_by')->nullable();
            $table->timestamps();

            // Add indexes for better performance
            $table->index(['bus_id', 'status']);
            $table->index(['filling_station_id', 'status']);
            $table->index('assigned_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bus_filling_station_assignments');
    }
};
