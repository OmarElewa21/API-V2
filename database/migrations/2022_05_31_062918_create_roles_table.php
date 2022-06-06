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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 32)->unique();
            $table->text('description')->nullable();
            $table->foreignId('permission_id')->constrained('permissions');
            $table->boolean('is_fixed')->default(false);
            $table->efficientUuid('uuid')->index()->unique()->nullable();
            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->timestamps();
        });

        DB::table('roles')->insert(
            array(
                'id'                    => 1,
                'name'                  => 'super admin',
                'permission_id'         => 1,
                'is_fixed'              => 1
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
};
