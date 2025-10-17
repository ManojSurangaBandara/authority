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
            $table->unsignedBigInteger('gs_division_id')->nullable()->after('district_id');
            $table->foreign('gs_division_id')->references('id')->on('gs_divisions')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('persons', function (Blueprint $table) {
            $table->dropForeign(['gs_division_id']);
            $table->dropColumn('gs_division_id');
        });
    }
};
