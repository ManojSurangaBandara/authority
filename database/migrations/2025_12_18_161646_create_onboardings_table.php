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
        Schema::create('onboardings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bus_pass_application_id');
            $table->unsignedBigInteger('escort_id');
            $table->unsignedBigInteger('bus_route_id')->nullable(); // For living out routes
            $table->unsignedBigInteger('living_in_bus_id')->nullable(); // For living in routes
            $table->string('route_type'); // 'living_out' or 'living_in'
            $table->string('branch_card_id');
            $table->string('serial_number')->nullable();
            $table->timestamp('onboarded_at');
            $table->json('boarding_data')->nullable(); // Store additional boarding information
            $table->timestamps();

            $table->foreign('bus_pass_application_id')->references('id')->on('bus_pass_applications')->onDelete('cascade');
            $table->foreign('escort_id')->references('id')->on('escorts')->onDelete('cascade');
            $table->foreign('bus_route_id')->references('id')->on('bus_routes')->onDelete('set null');
            $table->foreign('living_in_bus_id')->references('id')->on('living_in_buses')->onDelete('set null');

            $table->index(['bus_pass_application_id', 'onboarded_at']);
            $table->index(['escort_id', 'onboarded_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onboardings');
    }
};
