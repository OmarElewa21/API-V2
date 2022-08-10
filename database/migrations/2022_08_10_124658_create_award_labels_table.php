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
        Schema::create('award_labels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('award_id')->constrained();
            $table->unsignedTinyInteger('round_index')->nullable();
            $table->string('label');
            $table->float('min_points')->default(0);
            $table->float('percentage')->default(0);
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
        Schema::dropIfExists('award_labels');
    }
};
