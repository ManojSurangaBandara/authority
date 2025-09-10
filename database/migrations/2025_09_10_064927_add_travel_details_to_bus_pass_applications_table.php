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
        Schema::table('bus_pass_applications', function (Blueprint $table) {
            // Daily travel fields
            $table->string('daily_route_from')->nullable()->after('bus_pass_type');
            $table->string('daily_route_to')->nullable()->after('daily_route_from');
            $table->date('daily_start_date')->nullable()->after('daily_route_to');
            $table->date('daily_end_date')->nullable()->after('daily_start_date');
            $table->text('daily_reason')->nullable()->after('daily_end_date');
            
            // Weekend/Monthly travel fields
            $table->string('weekend_route_from')->nullable()->after('daily_reason');
            $table->string('weekend_route_to')->nullable()->after('weekend_route_from');
            $table->enum('weekend_frequency', ['weekly', 'monthly'])->nullable()->after('weekend_route_to');
            $table->date('weekend_start_date')->nullable()->after('weekend_frequency');
            $table->date('weekend_end_date')->nullable()->after('weekend_start_date');
            $table->text('weekend_reason')->nullable()->after('weekend_end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bus_pass_applications', function (Blueprint $table) {
            $table->dropColumn([
                'daily_route_from',
                'daily_route_to',
                'daily_start_date',
                'daily_end_date',
                'daily_reason',
                'weekend_route_from',
                'weekend_route_to',
                'weekend_frequency',
                'weekend_start_date',
                'weekend_end_date',
                'weekend_reason',
            ]);
        });
    }
};
