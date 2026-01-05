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
        Schema::table('slcmp_incharge_assignments', function (Blueprint $table) {
            $table->dropForeign(['route_id']);
        });
    }

    public function down(): void
    {
        Schema::table('slcmp_incharge_assignments', function (Blueprint $table) {
            $table->foreign('route_id')->references('id')->on('bus_routes')->onDelete('cascade');
        });
    }
};
