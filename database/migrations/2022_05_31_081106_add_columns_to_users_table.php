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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('roles');
            $table->string('username', 170)->unique();
            $table->boolean('permission_by_role')->default(true);
            $table->efficientUuid('uuid')->index()->unique()->nullable();
            $table->softDeletes($column = 'deleted_at', $precision = 0);
        });

        DB::table('users')->insert(
            array(
                'id'                    => 1,
                'name'                  => 'Super Admin',
                'email'                 => 'super_admin@simcc.com',
                'password'              => '$2a$12$lXDA2nuKj4k0sRLosSb/w.PO0x4RXm4k.GGva/wK0vhHL0e.IoUYm',
                'role_id'               => 1,
                'username'              => 'super_admin',
                'permission_by_role'    => 1
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
            $table->dropColumn('username');
            $table->dropColumn('deleted_at');
            $table->dropColumn('permission_by_role');
            $table->dropColumn('uuid');
        });
    }
};
