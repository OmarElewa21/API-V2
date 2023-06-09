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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->json('permissions_set');
            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->timestamps();
        });

        DB::table('permissions')->insert(array(
            array(
                'permissions_set'       => json_encode(['all' => true])
            ),
            array(
                'permissions_set'       => json_encode(['all' => false])
            ))
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permissions');
    }
};
