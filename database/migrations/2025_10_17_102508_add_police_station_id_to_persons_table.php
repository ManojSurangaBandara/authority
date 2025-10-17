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
            $table->unsignedBigInteger('police_station_id')->nullable()->after('district_id');
            $table->foreign('police_station_id')->references('id')->on('police_stations')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('persons', function (Blueprint $table) {
            $table->dropForeign(['police_station_id']);
            $table->dropColumn('police_station_id');
        });
    }
};
