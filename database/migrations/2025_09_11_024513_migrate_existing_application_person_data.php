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
        // Get all existing bus pass applications
        $applications = DB::table('bus_pass_applications')->get();

        foreach ($applications as $application) {
            // Check if person already exists in persons table
            $existingPerson = DB::table('persons')
                ->where('regiment_no', $application->regiment_no)
                ->first();

            if ($existingPerson) {
                // Use existing person
                $personId = $existingPerson->id;
            } else {
                // Create new person record
                $personId = DB::table('persons')->insertGetId([
                    'regiment_no' => $application->regiment_no,
                    'rank' => $application->rank,
                    'name' => $application->name,
                    'unit' => $application->unit,
                    'nic' => $application->nic,
                    'army_id' => $application->army_id,
                    'permanent_address' => $application->permanent_address,
                    'telephone_no' => $application->telephone_no,
                    'grama_seva_division' => $application->grama_seva_division,
                    'nearest_police_station' => $application->nearest_police_station,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Update application with person_id (add temporary column first)
            DB::statement('ALTER TABLE bus_pass_applications ADD COLUMN temp_person_id BIGINT UNSIGNED');
            DB::table('bus_pass_applications')
                ->where('id', $application->id)
                ->update(['temp_person_id' => $personId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the temporary person_id column if it exists
        if (Schema::hasColumn('bus_pass_applications', 'temp_person_id')) {
            Schema::table('bus_pass_applications', function (Blueprint $table) {
                $table->dropColumn('temp_person_id');
            });
        }
    }
};
