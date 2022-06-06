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
        // Schema::create('country_partners', function (Blueprint $table) {
        //     $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
        //     $table->foreignId('organization_id')->constrained('organizations');
        //     $table->string('country', 64);
        //     $table->softDeletes($column = 'deleted_at', $precision = 0);
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('country_partners');
    }
};
