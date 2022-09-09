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
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name', 164);
            $table->string('address', 240)->nullable();
            $table->string('postal_code', 16)->nullable();
            $table->string('phone', 24);
            $table->foreignId('country_id')->constrained();
            $table->foreignId('organization_id')->nullable()->constrained();
            $table->boolean('is_tuition_centre')->default(false);
            $table->string('province', 64)->nullable();
            $table->string('email')->unique();
            $table->enum('status', ['Approved', 'Pending', 'Deleted'])->default('Pending');
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
        Schema::dropIfExists('schools');
    }
};
