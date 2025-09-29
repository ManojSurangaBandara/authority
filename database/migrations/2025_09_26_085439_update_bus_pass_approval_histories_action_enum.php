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
            // Drop the existing enum column and recreate it with new values
            $table->dropColumn('action');
        });

        Schema::table('bus_pass_approval_histories', function (Blueprint $table) {
            // Add the column back with updated enum values
            $table->enum('action', ['approved', 'rejected', 'forwarded', 'recommended', 'not_recommended'])->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bus_pass_approval_histories', function (Blueprint $table) {
            // Drop the updated column
            $table->dropColumn('action');
        });

        Schema::table('bus_pass_approval_histories', function (Blueprint $table) {
            // Restore the original enum values
            $table->enum('action', ['approved', 'rejected', 'forwarded'])->after('user_id');
        });
    }
};
