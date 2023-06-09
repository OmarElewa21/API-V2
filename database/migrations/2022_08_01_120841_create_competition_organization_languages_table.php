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
        Schema::create('competition_organization_languages', function (Blueprint $table) {
            $table->foreignId('competition_id')->constrained();
            $table->foreignId('organization_id')->constrained();
            $table->foreignId('language_id')->constrained();
            $table->boolean('to_view')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('competition_organization_languages');
    }
};
