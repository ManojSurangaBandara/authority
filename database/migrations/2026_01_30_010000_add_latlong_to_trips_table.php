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
        Schema::table('trips', function (Blueprint $table) {
            $table->decimal('start_latitude', 10, 7)->nullable()->after('slcmp_incharge_id');
            $table->decimal('start_longitude', 10, 7)->nullable()->after('start_latitude');
            $table->decimal('end_latitude', 10, 7)->nullable()->after('trip_end_time');
            $table->decimal('end_longitude', 10, 7)->nullable()->after('end_latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn(['start_latitude', 'start_longitude', 'end_latitude', 'end_longitude']);
        });
    }
};
