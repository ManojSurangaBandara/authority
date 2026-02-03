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
        Schema::table('branch_card_switch_histories', function (Blueprint $table) {
            $table->text('old_temp_card_qr')->nullable()->after('old_branch_card_id');
        });
    }

    public function down(): void
    {
        Schema::table('branch_card_switch_histories', function (Blueprint $table) {
            $table->dropColumn('old_temp_card_qr');
        });
    }
};
