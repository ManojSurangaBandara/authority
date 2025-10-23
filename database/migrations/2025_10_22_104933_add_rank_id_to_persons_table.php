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
        Schema::table('persons', function (Blueprint $table) {
            $table->foreignId('rank_id')->nullable()->after('regiment_no')->constrained('ranks')->onDelete('set null');
            $table->dropColumn('rank');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('persons', function (Blueprint $table) {
            $table->string('rank', 50)->after('regiment_no');
            $table->dropForeign(['rank_id']);
            $table->dropColumn('rank_id');
        });
    }
};
