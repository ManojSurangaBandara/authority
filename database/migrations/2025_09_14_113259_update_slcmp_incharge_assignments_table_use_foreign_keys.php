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
            // Drop existing foreign key constraints first
            $table->dropForeign(['bus_route_id']);
        });
        
        Schema::table('slcmp_incharge_assignments', function (Blueprint $table) {
            // Drop the unique constraint (now possible since foreign key is gone)
            $table->dropUnique('unique_active_slcmp_per_route');
        });
        
        Schema::table('slcmp_incharge_assignments', function (Blueprint $table) {
            // Add the slcmp_incharge_id foreign key only if it doesn't exist
            if (!Schema::hasColumn('slcmp_incharge_assignments', 'slcmp_incharge_id')) {
                $table->foreignId('slcmp_incharge_id')->nullable()->constrained('slcmp_incharges')->onDelete('cascade')->after('bus_route_id');
            }
            
            // Drop the individual SLCMP detail columns
            $table->dropColumn([
                'slcmp_regiment_no',
                'slcmp_rank', 
                'slcmp_name',
                'slcmp_contact_no'
            ]);
            
            // Recreate foreign key for bus_route_id
            $table->foreign('bus_route_id')->references('id')->on('bus_routes')->onDelete('cascade');
            
            // Recreate the unique constraint with new structure
            $table->unique(['bus_route_id', 'status'], 'unique_active_slcmp_assignment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('slcmp_incharge_assignments', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['bus_route_id']);
            $table->dropForeign(['slcmp_incharge_id']);
            
            // Drop the new unique constraint
            $table->dropUnique('unique_active_slcmp_assignment');
        });
        
        Schema::table('slcmp_incharge_assignments', function (Blueprint $table) {
            // Add back the individual SLCMP detail columns
            $table->string('slcmp_regiment_no', 50)->after('bus_route_id');
            $table->string('slcmp_rank', 100)->after('slcmp_regiment_no');
            $table->string('slcmp_name', 200)->after('slcmp_rank');
            $table->string('slcmp_contact_no', 20)->after('slcmp_name');
            
            // Drop the slcmp_incharge_id column
            $table->dropColumn('slcmp_incharge_id');
            
            // Recreate the original foreign key and unique constraint
            $table->foreign('bus_route_id')->references('id')->on('bus_routes')->onDelete('cascade');
            $table->unique(['bus_route_id', 'status'], 'unique_active_slcmp_per_route');
        });
    }
};
