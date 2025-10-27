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
        Schema::table('persons', function (Blueprint $table) {
            // Add rank text column
            $table->string('rank', 50)->nullable()->after('regiment_no');
        });

        // Migrate existing rank_id data to rank text
        DB::statement('
            UPDATE persons
            SET rank = (
                SELECT CONCAT(ranks.abb_name, " - ", ranks.full_name)
                FROM ranks
                WHERE ranks.id = persons.rank_id
            )
            WHERE rank_id IS NOT NULL
        ');

        Schema::table('persons', function (Blueprint $table) {
            // Drop foreign key and rank_id column
            $table->dropForeign(['rank_id']);
            $table->dropColumn('rank_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('persons', function (Blueprint $table) {
            // Add rank_id foreign key column back
            $table->foreignId('rank_id')->nullable()->after('regiment_no')->constrained('ranks')->onDelete('set null');
        });

        // Try to migrate rank text back to rank_id (best effort)
        DB::statement('
            UPDATE persons
            SET rank_id = (
                SELECT ranks.id
                FROM ranks
                WHERE CONCAT(ranks.abb_name, " - ", ranks.full_name) = persons.rank
                LIMIT 1
            )
            WHERE rank IS NOT NULL
        ');

        Schema::table('persons', function (Blueprint $table) {
            // Drop rank text column
            $table->dropColumn('rank');
        });
    }
};
