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
        // Check if we need to drop existing foreign key constraints
        $foreignKeys = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_NAME = 'bus_driver_assignments' AND CONSTRAINT_NAME LIKE '%foreign%'");
        
        Schema::table('bus_driver_assignments', function (Blueprint $table) use ($foreignKeys) {
            // Drop existing foreign key constraints if they exist
            foreach ($foreignKeys as $fk) {
                if ($fk->CONSTRAINT_NAME === 'bus_driver_assignments_bus_route_id_foreign') {
                    $table->dropForeign(['bus_route_id']);
                    break;
                }
            }
        });
        
        // Check if unique constraint exists before dropping
        $uniqueConstraints = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_NAME = 'bus_driver_assignments' AND CONSTRAINT_TYPE = 'UNIQUE'");
        
        Schema::table('bus_driver_assignments', function (Blueprint $table) use ($uniqueConstraints) {
            // Drop the unique constraint if it exists
            foreach ($uniqueConstraints as $uc) {
                if ($uc->CONSTRAINT_NAME === 'unique_active_assignment') {
                    $table->dropUnique('unique_active_assignment');
                    break;
                }
            }
        });
        
        Schema::table('bus_driver_assignments', function (Blueprint $table) {
            // Add the driver_id foreign key only if it doesn't exist
            if (!Schema::hasColumn('bus_driver_assignments', 'driver_id')) {
                $table->foreignId('driver_id')->nullable()->constrained('drivers')->onDelete('cascade')->after('bus_route_id');
            }
            
            // Drop the individual driver detail columns if they exist
            $columns = Schema::getColumnListing('bus_driver_assignments');
            $columnsToRemove = ['driver_regiment_no', 'driver_rank', 'driver_name', 'driver_contact_no'];
            
            foreach ($columnsToRemove as $column) {
                if (in_array($column, $columns)) {
                    $table->dropColumn($column);
                }
            }
            
            // Recreate foreign key for bus_route_id
            $table->foreign('bus_route_id')->references('id')->on('bus_routes')->onDelete('cascade');
            
            // Recreate the unique constraint with new structure
            $table->unique(['bus_route_id', 'status'], 'unique_active_driver_assignment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bus_driver_assignments', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['bus_route_id']);
            $table->dropForeign(['driver_id']);
            
            // Drop the new unique constraint
            $table->dropUnique('unique_active_driver_assignment');
        });
        
        Schema::table('bus_driver_assignments', function (Blueprint $table) {
            // Add back the individual driver detail columns
            $table->string('driver_regiment_no', 50)->after('bus_route_id');
            $table->string('driver_rank', 100)->after('driver_regiment_no');
            $table->string('driver_name', 200)->after('driver_rank');
            $table->string('driver_contact_no', 20)->after('driver_name');
            
            // Drop the driver_id column
            $table->dropColumn('driver_id');
            
            // Recreate the original foreign key and unique constraint
            $table->foreign('bus_route_id')->references('id')->on('bus_routes')->onDelete('cascade');
            $table->unique(['bus_route_id', 'status'], 'unique_active_assignment');
        });
    }
};
