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
        Schema::create('bus_pass_applications', function (Blueprint $table) {
            $table->id();
            
            // Personal Information (from API)
            $table->string('regiment_no');
            $table->string('rank');
            $table->string('name');
            $table->string('unit');
            $table->string('nic');
            $table->string('army_id');
            $table->text('permanent_address');
            $table->string('telephone_no');
            $table->string('grama_seva_division');
            $table->string('nearest_police_station');
            
            // Application Specific Information
            $table->string('branch_directorate');
            $table->enum('marital_status', ['single', 'married']);
            $table->enum('approval_living_out', ['yes', 'no']);
            $table->enum('obtain_sltb_season', ['yes', 'no']);
            $table->date('date_arrival_ahq');
            
            // File uploads
            $table->string('grama_niladari_certificate')->nullable();
            $table->string('person_image')->nullable();
            
            // Bus Pass Type
            $table->enum('bus_pass_type', ['daily_travel', 'weekend_monthly_travel']);
            
            // Daily Travel Fields (for Living Out)
            $table->string('requested_bus_name')->nullable();
            $table->string('destination_from_ahq')->nullable();
            $table->string('rent_allowance_order')->nullable(); // file upload
            
            // Weekend/Monthly Travel Fields (for Living In)
            $table->string('living_in_bus')->nullable();
            $table->string('destination_location_ahq')->nullable();
            $table->string('weekend_bus_name')->nullable();
            $table->string('weekend_destination')->nullable();
            
            // Declarations
            $table->enum('declaration_1', ['yes', 'no']);
            $table->enum('declaration_2', ['yes', 'no']);
            
            // Status and workflow
            $table->enum('status', ['pending', 'approved_by_staff', 'approved_by_director', 'forwarded_to_movement', 'approved', 'rejected'])->default('pending');
            $table->text('remarks')->nullable();
            $table->string('created_by'); // user who created the application
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bus_pass_applications');
    }
};
