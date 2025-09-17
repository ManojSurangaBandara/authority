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
        Schema::create('bus_pass_approval_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bus_pass_application_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('action', ['approved', 'rejected', 'forwarded']);
            $table->string('previous_status', 50);
            $table->string('new_status', 50);
            $table->text('remarks')->nullable();
            $table->timestamp('action_date');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('bus_pass_application_id')->references('id')->on('bus_pass_applications')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes for better performance with shorter names
            $table->index(['bus_pass_application_id', 'action_date'], 'bpah_app_date_idx');
            $table->index('user_id', 'bpah_user_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bus_pass_approval_histories');
    }
};
