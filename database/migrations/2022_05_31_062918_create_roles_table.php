<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Role;

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
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->efficientUuid('uuid')->index()->unique()->nullable();
            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->timestamps();
        });

        $insertions = [
            ['name' => 'super admin', 'permission_id' => 1],
            ['name' => 'admin', 'permission_id' => 1],
            ['name' => 'country partner', 'permission_id' => 2],
            ['name' => 'country partner assistant', 'permission_id' => 2],
            ['name' => 'school manager', 'permission_id' => 2],
            ['name' => 'teacher', 'permission_id' => 2]
        ];

        foreach($insertions as $insertion){
            Role::create(array_merge($insertion, ['is_fixed' => 1]));
        }
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
