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
        Schema::create('task_domains', function (Blueprint $table) {
            $table->foreignId('task_id')->constrained();
            $table->unsignedInteger('relation_id');
            $table->string('relation_type');
            $table->boolean('is_tag')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task_domains');
    }
};
