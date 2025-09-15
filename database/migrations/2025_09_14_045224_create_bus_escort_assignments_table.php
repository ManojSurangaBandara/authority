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
        Schema::create('bus_escort_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_route_id')->constrained('bus_routes')->onDelete('cascade');
            $table->string('escort_regiment_no', 50);
            $table->string('escort_rank', 100);
            $table->string('escort_name', 200);
            $table->string('escort_contact_no', 20);
            $table->date('assigned_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('created_by', 100)->nullable();
            $table->timestamps();

            // Unique constraint to ensure only one active escort per route
            $table->unique(['bus_route_id', 'status'], 'unique_active_escort_per_route')
                  ->where('status', 'active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bus_escort_assignments');
    }
};
