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
        Schema::create('awards', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('round_index')->nullable();
            $table->boolean('by_position')->default(false);
            $table->boolean('use_grade_to_assign_points')->default(false);
            $table->float('min_points')->default(0);
            $table->boolean('use_min_points_for_all')->default(false);
            $table->string('default_award')->nullable();
            $table->string('default_points')->nullable();
            $table->boolean('is_overall')->default(false);
            $table->efficientUuid('uuid')->index()->unique()->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
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
        Schema::dropIfExists('awards');
    }
};
