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
        Schema::dropIfExists('user_types');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('user_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('category'); // 'branch' or 'movement'
            $table->text('description')->nullable();
            $table->json('permissions')->nullable();
            $table->integer('hierarchy_level')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
};
