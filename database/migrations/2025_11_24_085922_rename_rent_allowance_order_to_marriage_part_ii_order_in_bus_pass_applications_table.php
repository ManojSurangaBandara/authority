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
            // Rename the column from rent_allowance_order to marriage_part_ii_order
            $table->renameColumn('rent_allowance_order', 'marriage_part_ii_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bus_pass_applications', function (Blueprint $table) {
            // Rename the column back from marriage_part_ii_order to rent_allowance_order
            $table->renameColumn('marriage_part_ii_order', 'rent_allowance_order');
        });
    }
};
