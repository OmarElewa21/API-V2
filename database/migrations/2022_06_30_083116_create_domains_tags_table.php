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
        Schema::create('domains_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->set('status', ['Approved', 'Pending', 'Deleted'])->default('Pending');
            $table->boolean('is_tag')->default(false);
            $table->efficientUuid('uuid')->index()->unique()->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('domains_tags');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->foreignId('deleted_by')->nullable()->constrained('users');
            $table->dateTime('approved_at')->nullable();
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
        Schema::dropIfExists('domains_tags');
    }
};
