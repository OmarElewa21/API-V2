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
        Schema::create('task_answer_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('answer_id')->constrained('task_answers');
            $table->foreignId('lang_id')->constrained('languages');
            $table->text('label')->nullable();
            $table->text('content')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task_answer_contents');
    }
};
