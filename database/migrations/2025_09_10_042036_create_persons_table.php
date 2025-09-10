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
        Schema::create('persons', function (Blueprint $table) {
            $table->id();
            $table->string('regiment_no')->unique();
            $table->string('rank');
            $table->string('name');
            $table->string('unit');
            $table->string('nic');
            $table->string('army_id');
            $table->text('permanent_address');
            $table->string('telephone_no');
            $table->string('grama_seva_division');
            $table->string('nearest_police_station');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persons');
    }
};
