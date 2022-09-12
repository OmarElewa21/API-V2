<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('round_level_participant', function (Blueprint $table) {
            $table->foreignId('participant_id')->constrained();
            $table->foreignId('round_level_id')->constrained();
            $table->foreignId('session_id')->nullable()->constrained();
            $table->foreignId('competition_team_id')->nullable()->constrained();
            $table->enum('status', ['Active', 'In Progress', 'Completed', 'Inactive', 'Banned'])->default('Active');
            $table->foreignId('assigned_by')->nullable()->constrained('users');
            $table->dateTime('assigned_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('round_level_participant');
    }
};
