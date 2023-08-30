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
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('job_id')->constrained('jobs')->onDelete('cascade');
            $table->foreignId('applicant_id')->constrained('applicants')->onDelete('cascade');
            $table->unsignedInteger('stage')->comment('ID from application_stages table');
            $table->string('photo')->nullable();
            $table->text('cover_letter')->nullable();
            $table->integer('sequence')->unsigned()->default(0);
            $table->unsignedSmallInteger('rating')->nullable();
            $table->boolean('is_seen')->default(0);

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
        Schema::dropIfExists('job_applications');
    }
};
