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
        // Add person_id column only if it doesn't exist
        if (!Schema::hasColumn('bus_pass_applications', 'person_id')) {
            Schema::table('bus_pass_applications', function (Blueprint $table) {
                $table->unsignedBigInteger('person_id')->nullable()->after('id');
            });
        }

        // Only copy data if temp_person_id column exists (from data migration)
        if (Schema::hasColumn('bus_pass_applications', 'temp_person_id')) {
            DB::statement('UPDATE bus_pass_applications SET person_id = temp_person_id WHERE temp_person_id IS NOT NULL');
            
            Schema::table('bus_pass_applications', function (Blueprint $table) {
                // Drop temporary column
                $table->dropColumn('temp_person_id');
            });
        }

        // Check if foreign key exists before adding it
        $foreignKeys = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_NAME = 'bus_pass_applications' AND CONSTRAINT_NAME LIKE '%person_id_foreign%'");
        
        if (empty($foreignKeys)) {
            Schema::table('bus_pass_applications', function (Blueprint $table) {
                // Add foreign key constraint
                $table->foreign('person_id')->references('id')->on('persons')->onUpdate('cascade')->onDelete('cascade');
            });
        }

        Schema::table('bus_pass_applications', function (Blueprint $table) {
            // Only drop person-related fields if they exist
            $columnsToRemove = ['regiment_no', 'rank', 'name', 'unit', 'nic', 'army_id', 'permanent_address', 'telephone_no', 'grama_seva_division', 'nearest_police_station'];
            $existingColumns = Schema::getColumnListing('bus_pass_applications');
            
            $columnsToActuallyRemove = array_intersect($columnsToRemove, $existingColumns);
            
            if (!empty($columnsToActuallyRemove)) {
                $table->dropColumn($columnsToActuallyRemove);
            }
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
