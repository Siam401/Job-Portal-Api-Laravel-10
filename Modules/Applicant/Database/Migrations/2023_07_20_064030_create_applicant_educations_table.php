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
        Schema::create('applicant_educations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('applicant_id')->constrained('applicants')->cascadeOnDelete();
            $table->foreignId('education_id')->constrained('educations')->cascadeOnDelete();
            $table->string('degree', 100)->nullable();
            $table->string('major', 100)->nullable();
            $table->string('board', 100)->nullable();
            $table->string('institute', 100)->nullable();
            $table->string('result', 20)->nullable()->comment('Values: grade, division, class');
            $table->string('mark', 20)->nullable()->comment('Marks obtained / CGPA');
            $table->string('scale', 20)->nullable()->comment('Scale or Total marks');
            $table->year('passing_year')->nullable();
            $table->tinyInteger('duration')->nullable()->comment('In years');
            $table->string('achievement')->nullable();

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
        Schema::dropIfExists('applicant_educations');
    }
};
