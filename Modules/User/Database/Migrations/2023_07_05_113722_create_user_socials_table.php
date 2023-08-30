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
        Schema::create('user_socials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('type', 12)->comment('Values: facebook, twitter, google');
            $table->string('social_id');
            $table->mediumText('meta')->nullable()->comment('save user information after social registration');

            $table->unique(['type', 'social_id'], 'social_code');
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
        Schema::dropIfExists('user_socials');
        Schema::dropIfExists('applicant_socials');
    }
};
