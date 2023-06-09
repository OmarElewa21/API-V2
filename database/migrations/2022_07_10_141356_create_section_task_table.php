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
        Schema::create('section_task', function (Blueprint $table) {
            $table->foreignId('section_id')->constrained();
            $table->foreignId('task_id')->constrained();
            $table->unsignedSmallInteger('index')->nullable();
            $table->boolean('in_group')->default(false);
            $table->unsignedSmallInteger('group_index')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('section_task');
    }
};
