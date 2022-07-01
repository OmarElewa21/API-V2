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
        Schema::create('difficulty_group_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('difficulty_group_id')->constrained('difficulty_groups');
            $table->smallInteger('correct_points');
            $table->smallInteger('wrong_points');
            $table->smallInteger('blank_points');
            $table->softDeletes($column = 'deleted_at', $precision = 0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('difficulty_group_levels');
    }
};
