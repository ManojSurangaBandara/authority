<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First update the enum to include both old and new values temporarily
        DB::statement("ALTER TABLE branch_card_switch_histories MODIFY COLUMN action ENUM('switched_to_branch_card', 'verification_failed', 'switched_from_temp_card_to_branch_card', 'switched_branch_cards') NOT NULL");

        // Delete verification_failed records as they're no longer needed
        DB::table('branch_card_switch_histories')->where('action', 'verification_failed')->delete();

        // Update successful switch records to new action value
        DB::table('branch_card_switch_histories')
            ->where('action', 'switched_to_branch_card')
            ->update(['action' => 'switched_from_temp_card_to_branch_card']);

        // Update the enum to final values
        DB::statement("ALTER TABLE branch_card_switch_histories MODIFY COLUMN action ENUM('switched_from_temp_card_to_branch_card', 'switched_branch_cards') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the enum values
        DB::statement("ALTER TABLE branch_card_switch_histories MODIFY COLUMN action ENUM('switched_to_branch_card', 'verification_failed') NOT NULL");

        // Revert the action values
        DB::table('branch_card_switch_histories')
            ->where('action', 'switched_from_temp_card_to_branch_card')
            ->update(['action' => 'switched_to_branch_card']);
    }
};
