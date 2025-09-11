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
        Schema::table('bus_pass_applications', function (Blueprint $table) {
            // Change status column from enum to string to support new status codes
            $table->string('status', 50)->default('pending_subject_clerk')->change();

            // Add foreign key constraint to bus_pass_statuses table
            $table->foreign('status')->references('code')->on('bus_pass_statuses')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bus_pass_applications', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['status']);

            // Revert back to enum (for rollback compatibility)
            $table->enum('status', ['pending', 'approved_by_staff', 'approved_by_director', 'forwarded_to_movement', 'approved', 'rejected'])->default('pending')->change();
        });
    }
};
