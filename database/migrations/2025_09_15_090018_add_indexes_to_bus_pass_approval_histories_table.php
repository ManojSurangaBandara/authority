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
        Schema::table('bus_pass_approval_histories', function (Blueprint $table) {
            // Only add indexes (foreign keys were already created in the original migration)
            $table->index(['bus_pass_application_id', 'action_date'], 'bpah_app_date_idx');
            $table->index('user_id', 'bpah_user_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bus_pass_approval_histories', function (Blueprint $table) {
            $table->dropIndex('bpah_app_date_idx');
            $table->dropIndex('bpah_user_idx');
        });
    }
};
