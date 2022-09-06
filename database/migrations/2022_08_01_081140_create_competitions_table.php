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
        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('identifier')->unique();
            $table->date('global_competition_start_date')->nullabe();
            $table->date('global_competition_end_date')->nullabe();
            $table->boolean('re_run')->default(0);
            $table->set('format', ['Local', 'Global'])->default('Local');
            $table->set('mode', ['Online', 'Paper-Based', 'Both'])->default('Online');
            $table->foreignId('difficulty_group_id')->constrained();
            $table->json('grades')->nullable();
            $table->text('instructions')->nullable();
            $table->set('status', ['Active', 'Computed', 'Closed'])->default('Active');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->foreignId('deleted_by')->nullable()->constrained('users');
            $table->efficientUuid('uuid')->index()->unique()->nullable();
            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->timestamps();
        });
        Schema::table('competitions', function (Blueprint $table) {
            $table->foreignId('competition_reference')->nullable()->constrained('competitions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('competitions');
    }
};
