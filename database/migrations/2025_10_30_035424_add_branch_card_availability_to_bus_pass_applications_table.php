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
            $table->enum('branch_card_availability', ['has_branch_card', 'no_branch_card'])->nullable()->after('obtain_sltb_season')
                ->comment('Whether the person has a branch card (integrate) or needs temporary card (print)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bus_pass_applications', function (Blueprint $table) {
            $table->dropColumn('branch_card_availability');
        });
    }
};
