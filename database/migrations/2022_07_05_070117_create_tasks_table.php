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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('identifier', 64);
            $table->text('description')->nullable();
            $table->string('img')->nullable();
            $table->text('solution_working')->nullable();
            $table->json('recommendations')->nullable();
            $table->string('status', 32)->default('pending');
            $table->string('answer_type', 32)->default('MCQ');
            $table->string('answer_layout', 32)->default('Horizontal');
            $table->string('answer_structure', 32)->default('Default');
            $table->string('answer_sorting', 32)->default('Fix Order');
            $table->boolean('answers_as_img')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->dateTime('approved_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->foreignId('deleted_by')->nullable()->constrained('users');
            $table->efficientUuid('uuid')->index()->unique()->nullable();
            $table->softDeletes($column = 'deleted_at', $precision = 0);
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
        Schema::dropIfExists('tasks');
    }
};
