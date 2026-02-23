<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * We'll add a driver_type column (Army/Civil) and a nullable NIC field.
     * The existing regiment_no column is already unique; for civil drivers it
     * will be allowed to remain null.  We also make sure NIC is unique.
     */
    public function up()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->string('driver_type')->default('Army')->after('id');
            $table->string('nic')->nullable()->unique()->after('regiment_no');
            // make regiment_no nullable since civil drivers won't have it
            $table->string('regiment_no')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn('driver_type');
            $table->dropUnique(['nic']);
            $table->dropColumn('nic');
            // revert regiment_no to not nullable (if needed)
            $table->string('regiment_no')->nullable(false)->change();
        });
    }
};
