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
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->string('email', 100)->unique();
            $table->string('country_code', 5)->nullable();
            $table->string('mobile', 15)->nullable();
            $table->string('first_name', 100);
            $table->string('last_name', 100)->nullable();
            $table->string('gender', 10)->default('male');
            $table->date('dob')->nullable();
            $table->string('resume')->nullable();
            $table->string('photo')->nullable();
            $table->boolean('is_favorite')->default(0);
            $table->boolean('is_cv_parsed')->default(0);

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
        Schema::dropIfExists('applicants');
    }
};
