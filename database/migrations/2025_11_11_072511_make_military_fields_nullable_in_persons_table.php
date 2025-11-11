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
            // Make military-specific fields nullable to support both civil and military persons
            $table->string('regiment_no')->nullable()->change();
            $table->string('rank')->nullable()->change();
            $table->string('army_id')->nullable()->change();
            $table->string('unit')->nullable()->change();

            // Remove unique constraint from regiment_no since civil persons won't have it
            $table->dropUnique(['regiment_no']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('persons', function (Blueprint $table) {
            // Revert military-specific fields back to required (not nullable)
            $table->string('regiment_no')->nullable(false)->change();
            $table->string('rank')->nullable(false)->change();
            $table->string('army_id')->nullable(false)->change();
            $table->string('unit')->nullable(false)->change();

            // Add back unique constraint to regiment_no
            $table->unique('regiment_no');
        });
    }
};
