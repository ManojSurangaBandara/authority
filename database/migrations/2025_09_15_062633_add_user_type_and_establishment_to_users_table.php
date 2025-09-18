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
            // Add user type reference
            $table->foreignId('user_type_id')->nullable()->after('email')->constrained('user_types')->onDelete('set null');
            
            // Add establishment reference (for branch/directorate users)
            $table->foreignId('establishment_id')->nullable()->after('user_type_id')->constrained('establishments')->onDelete('set null');
            
            // Add additional fields from specification
            $table->string('regiment_no', 50)->nullable()->after('establishment_id');
            $table->string('rank', 100)->nullable()->after('regiment_no');
            $table->string('contact_no', 20)->nullable()->after('rank');
            $table->boolean('is_active')->default(true)->after('contact_no');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['user_type_id']);
            $table->dropForeign(['establishment_id']);
            $table->dropColumn([
                'user_type_id',
                'establishment_id',
                'regiment_no',
                'rank',
                'contact_no',
                'is_active',
                'last_login_at'
            ]);
        });
    }
};
