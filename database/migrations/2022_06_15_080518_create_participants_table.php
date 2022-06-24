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
        Schema::create('participants', function (Blueprint $table) {
            $table->string('index', 12)->unique();
            $table->string('password');
            $table->string('name', 132);
            $table->string('class', 32);
            $table->string('grade', 32);
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('school_id')->nullable()->constrained('schools');
            $table->foreignId('country_id')->constrained('countries');
            $table->unsignedBigInteger('tuition_centre_id')->nullable();
            $table->foreign('tuition_centre_id')->references('id')->on('schools');
            $table->efficientUuid('uuid')->index()->unique()->nullable();
            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->foreignId('deleted_by')->nullable()->constrained('users');
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
        Schema::dropIfExists('participants');
    }
};
