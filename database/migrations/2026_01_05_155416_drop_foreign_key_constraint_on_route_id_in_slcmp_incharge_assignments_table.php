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
        // No foreign key was created for route_id in the migration that added it,
        // so there's nothing to drop here. This migration can be skipped.
    }

    public function down(): void
    {
        // No foreign key was created for route_id, so nothing to restore.
    }
};
