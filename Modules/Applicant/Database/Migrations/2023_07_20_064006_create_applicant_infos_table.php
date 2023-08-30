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
        Schema::create('applicant_infos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('applicant_id')->constrained('applicants')->cascadeOnDelete();
            $table->string('father_name', 50)->nullable();
            $table->string('mother_name', 50)->nullable();
            $table->string('religion', 20)->nullable();
            $table->string('marital_status', 20)->nullable();
            $table->string('nationality', 50)->nullable();
            $table->string('nid', 50)->nullable();
            $table->string('secondary_countrycode', 5)->nullable();
            $table->string('secondary_mobile', 20)->nullable();
            $table->string('alternate_email', 100)->nullable();
            $table->float('height', 8, 1)->nullable();
            $table->float('weight', 8, 1)->nullable();
            $table->text('present_address')->nullable();
            $table->boolean('is_same_address')->default(1);
            $table->text('permanent_address')->nullable();
            $table->text('career_objective')->nullable();
            $table->integer('present_salary')->nullable();
            $table->integer('expected_salary')->nullable();
            $table->string('job_level', 20)->nullable();
            $table->string('job_type', 20)->nullable()->comment('Job Nature: Full-time, Part-time etc.');
            $table->string('preferred_functions')->nullable()->comment('Array of Job Functions ID');
            $table->string('special_skills')->nullable()->comment('Array of Special Skills ID');
            $table->text('career_summary')->nullable();
            $table->text('special_qualification')->nullable();
            $table->boolean('has_disability')->default(0);
            $table->string('disability_id')->nullable();
            // $table->text('other_skills')->nullable();

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
        Schema::dropIfExists('applicant_infos');
    }
};
