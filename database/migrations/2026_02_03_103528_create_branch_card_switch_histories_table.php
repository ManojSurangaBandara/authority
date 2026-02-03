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
        Schema::create('branch_card_switch_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bus_pass_application_id');
            $table->string('regiment_no');
            $table->string('old_branch_card_id')->nullable();
            $table->string('new_branch_card_id');
            $table->enum('action', ['switched_from_temp_card_to_branch_card', 'switched_branch_cards']);
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('performed_by');
            $table->timestamps();

            $table->foreign('bus_pass_application_id')->references('id')->on('bus_pass_applications');
            $table->foreign('performed_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_card_switch_histories');
    }
};
