<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('route_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('route_group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_group_id')->constrained('route_groups')->onDelete('cascade');
            $table->enum('route_type', ['living_out', 'living_in']);
            $table->unsignedBigInteger('route_id');
            $table->timestamps();

            $table->unique(['route_group_id', 'route_type', 'route_id'], 'route_group_member_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('route_group_members');
        Schema::dropIfExists('route_groups');
    }
};
