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
        Schema::create('bus_route_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_id')->constrained('buses')->onDelete('cascade');
            $table->unsignedBigInteger('route_id');
            $table->enum('route_type', ['living_out', 'living_in']);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            // We'll handle the unique constraint for active assignments in the application layer
            // to avoid issues with inactive records
            $table->index(['bus_id', 'status']);

            // Add index for better performance
            $table->index(['route_id', 'route_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bus_route_assignments');
    }
};
