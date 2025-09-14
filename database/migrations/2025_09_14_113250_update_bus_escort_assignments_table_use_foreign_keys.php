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
        Schema::table('bus_escort_assignments', function (Blueprint $table) {
            // Drop existing foreign key constraints first
            $table->dropForeign(['bus_route_id']);
        });
        
        Schema::table('bus_escort_assignments', function (Blueprint $table) {
            // Drop the unique constraint (now possible since foreign key is gone)
            $table->dropUnique('unique_active_escort_per_route');
        });
        
        Schema::table('bus_escort_assignments', function (Blueprint $table) {
            // Add the escort_id foreign key only if it doesn't exist
            if (!Schema::hasColumn('bus_escort_assignments', 'escort_id')) {
                $table->foreignId('escort_id')->nullable()->constrained('escorts')->onDelete('cascade')->after('bus_route_id');
            }
            
            // Drop the individual escort detail columns
            $table->dropColumn([
                'escort_regiment_no',
                'escort_rank', 
                'escort_name',
                'escort_contact_no'
            ]);
            
            // Recreate foreign key for bus_route_id
            $table->foreign('bus_route_id')->references('id')->on('bus_routes')->onDelete('cascade');
            
            // Recreate the unique constraint with new structure
            $table->unique(['bus_route_id', 'status'], 'unique_active_escort_assignment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bus_escort_assignments', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['bus_route_id']);
            $table->dropForeign(['escort_id']);
            
            // Drop the new unique constraint
            $table->dropUnique('unique_active_escort_assignment');
        });
        
        Schema::table('bus_escort_assignments', function (Blueprint $table) {
            // Add back the individual escort detail columns
            $table->string('escort_regiment_no', 50)->after('bus_route_id');
            $table->string('escort_rank', 100)->after('escort_regiment_no');
            $table->string('escort_name', 200)->after('escort_rank');
            $table->string('escort_contact_no', 20)->after('escort_name');
            
            // Drop the escort_id column
            $table->dropColumn('escort_id');
            
            // Recreate the original foreign key and unique constraint
            $table->foreign('bus_route_id')->references('id')->on('bus_routes')->onDelete('cascade');
            $table->unique(['bus_route_id', 'status'], 'unique_active_escort_per_route');
        });
    }
};
