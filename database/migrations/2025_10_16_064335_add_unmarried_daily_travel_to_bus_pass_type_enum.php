<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'unmarried_daily_travel' to the bus_pass_type enum
        DB::statement("ALTER TABLE bus_pass_applications MODIFY COLUMN bus_pass_type ENUM('daily_travel', 'weekend_monthly_travel', 'living_in_only', 'weekend_only', 'unmarried_daily_travel') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'unmarried_daily_travel' from the bus_pass_type enum
        DB::statement("ALTER TABLE bus_pass_applications MODIFY COLUMN bus_pass_type ENUM('daily_travel', 'weekend_monthly_travel', 'living_in_only', 'weekend_only') NOT NULL");
    }
};
