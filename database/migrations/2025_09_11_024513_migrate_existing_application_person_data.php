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
        // Skip if the person data columns no longer exist (already migrated)
        if (!Schema::hasColumn('bus_pass_applications', 'regiment_no')) {
            // Data has already been migrated, nothing to do
            return;
        }

        // First, add the temp_person_id column if it doesn't exist
        if (!Schema::hasColumn('bus_pass_applications', 'temp_person_id')) {
            Schema::table('bus_pass_applications', function (Blueprint $table) {
                $table->unsignedBigInteger('temp_person_id')->nullable();
            });
        }

        // Get all existing bus pass applications that don't have person_id set
        $applications = DB::table('bus_pass_applications')
            ->whereNull('person_id')
            ->whereNotNull('regiment_no') // Only process if we have regiment_no data
            ->get();

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
                    'rank' => $application->rank ?? '',
                    'name' => $application->name ?? '',
                    'unit' => $application->unit ?? '',
                    'nic' => $application->nic ?? '',
                    'army_id' => $application->army_id ?? '',
                    'permanent_address' => $application->permanent_address ?? '',
                    'telephone_no' => $application->telephone_no ?? '',
                    'grama_seva_division' => $application->grama_seva_division ?? '',
                    'nearest_police_station' => $application->nearest_police_station ?? '',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Update application with temp_person_id
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
