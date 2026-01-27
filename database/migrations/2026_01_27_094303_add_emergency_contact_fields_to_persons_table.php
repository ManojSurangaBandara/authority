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
            $table->string('blood_group')->nullable()->after('telephone_no');
            $table->string('nok_name')->nullable()->after('blood_group');
            $table->string('nok_telephone_no')->nullable()->after('nok_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('persons', function (Blueprint $table) {
            $table->dropColumn(['blood_group', 'nok_name', 'nok_telephone_no']);
        });
    }
};
