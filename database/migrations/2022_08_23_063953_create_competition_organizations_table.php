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
        Schema::create('competition_organizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained();
            $table->foreignId('organization_id')->constrained();
            $table->boolean('allow_session_edits_by_partners')->default(true);
            $table->date('registration_open');
            $table->json('competition_dates');
            $table->set('status', ['Active', 'Locked', 'Ready', 'Rejected', 'Pending'])->default('Active');
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
        Schema::dropIfExists('competition_organizations');
    }
};
