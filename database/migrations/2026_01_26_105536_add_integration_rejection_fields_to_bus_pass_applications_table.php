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
            $table->text('integration_rejection_remarks')->nullable();
            $table->timestamp('integration_rejected_at')->nullable();
            $table->unsignedBigInteger('integration_rejected_by')->nullable();
            $table->foreign('integration_rejected_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bus_pass_applications', function (Blueprint $table) {
            $table->dropForeign(['integration_rejected_by']);
            $table->dropColumn(['integration_rejection_remarks', 'integration_rejected_at', 'integration_rejected_by']);
        });
    }
};
