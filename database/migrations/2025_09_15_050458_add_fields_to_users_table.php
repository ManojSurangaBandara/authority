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
        Schema::table('users', function (Blueprint $table) {
            $table->string('regiment_no')->nullable()->after('name');
            $table->string('rank')->nullable()->after('regiment_no');
            $table->unsignedBigInteger('establishment_id')->nullable()->after('rank');
            $table->enum('user_type', ['branch', 'movement'])->default('branch')->after('establishment_id');
            $table->boolean('is_active')->default(true)->after('user_type');
            
            // Add foreign key for establishment (we'll create this table next)
            $table->foreign('establishment_id')->references('id')->on('establishments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['establishment_id']);
            $table->dropColumn(['regiment_no', 'rank', 'establishment_id', 'user_type', 'is_active']);
        });
    }
};
