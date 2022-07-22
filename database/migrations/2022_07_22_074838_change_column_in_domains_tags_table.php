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
        Schema::table('domains_tags', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('domains_tags', function (Blueprint $table) {
            $table->unsignedTinyInteger('status')->default(1);
            $table->foreignId('parent_id')->nullable()->constrained('domains_tags');
        });
        Schema::dropIfExists('topics');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('domains_tags', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });

        Schema::table('domains_tags', function (Blueprint $table) {
            $table->string('status', 16)->default('pending');
        });
    }
};
