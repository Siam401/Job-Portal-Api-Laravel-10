<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('job_id')->constrained('jobs')->cascadeOnDelete();
            $table->string('job_type', 32)->nullable();
            $table->string('workplace', 32)->nullable();
            $table->string('salary')->nullable();
            $table->string('gender', 12)->nullable()->comment('male, female, transgender, other');
            $table->string('form_visibility')->nullable()->comment('Custom form input filed which will be visible as inputs in comma separated string');
            $table->string('questions')->nullable()->comment('Job questions ID as comma separated string');
            $table->text('skills')->nullable()->comment('Skills as words/data array in JSON');
            $table->text('description');
            $table->text('responsibility');
            $table->text('education')->nullable();
            $table->text('benefit');
            $table->text('additional')->nullable();
            $table->smallInteger('age_min')->nullable();
            $table->smallInteger('age_max')->nullable();
            $table->boolean('is_exp_required')->default(0)->comment('Does experience is required, 0: yes, 1: no');
            $table->text('experience')->nullable()->comment('Job experience requirement');
            $table->smallInteger('min_exp')->nullable();
            $table->smallInteger('max_exp')->nullable();

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
        Schema::dropIfExists('job_details');
    }
};
