<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE bus_pass_approval_histories MODIFY COLUMN action ENUM('approved', 'rejected', 'forwarded', 'recommended', 'not_recommended', 'dmov_not_recommended', 'forwarded_to_branch_clerk', 'route_updated', 'integration_rejected') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE bus_pass_approval_histories MODIFY COLUMN action ENUM('approved', 'rejected', 'forwarded', 'recommended', 'not_recommended', 'dmov_not_recommended', 'forwarded_to_branch_clerk', 'route_updated') NOT NULL");
    }
};
