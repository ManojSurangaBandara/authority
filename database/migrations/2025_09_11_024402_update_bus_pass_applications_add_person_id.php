<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bus_pass_applications', function (Blueprint $table) {
            // Add person_id column based on temp_person_id values
            $table->unsignedBigInteger('person_id')->after('id');
        });

        // Copy data from temp_person_id to person_id
        DB::statement('UPDATE bus_pass_applications SET person_id = temp_person_id');

        Schema::table('bus_pass_applications', function (Blueprint $table) {
            // Add foreign key constraint
            $table->foreign('person_id')->references('id')->on('persons')->onUpdate('cascade')->onDelete('cascade');

            // Drop temporary column
            $table->dropColumn('temp_person_id');

            // Remove person-related fields that will now be stored in persons table
            $table->dropColumn([
                'regiment_no',
                'rank',
                'name',
                'unit',
                'nic',
                'army_id',
                'permanent_address',
                'telephone_no',
                'grama_seva_division',
                'nearest_police_station'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bus_pass_applications', function (Blueprint $table) {
            // Drop foreign key and person_id column
            $table->dropForeign(['person_id']);
            $table->dropColumn('person_id');

            // Re-add person-related fields
            $table->string('regiment_no')->after('id');
            $table->string('rank')->after('regiment_no');
            $table->string('name')->after('rank');
            $table->string('unit')->after('name');
            $table->string('nic')->after('unit');
            $table->string('army_id')->after('nic');
            $table->text('permanent_address')->after('army_id');
            $table->string('telephone_no')->after('permanent_address');
            $table->string('grama_seva_division')->after('telephone_no');
            $table->string('nearest_police_station')->after('grama_seva_division');
        });
    }
};
