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
        Schema::create('job_interviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('hr_id')->constrained('users')->comment('Which Admin/HR has set the interview');
            $table->foreignId('job_application_id')->constrained('job_applications')->cascadeOnDelete();
            $table->date('interview_date');
            $table->time('interview_time');
            $table->text('address')->nullable()->comment('Interview address');
            $table->text('message_email')->nullable()->comment('Interview email message');
            $table->text('message_sms')->nullable()->comment('Interview sms message');
            $table->tinyInteger('status')->default(0)->comment('0: Not called, 1: Called, 2: Done, 3: Cancelled');

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
        Schema::dropIfExists('job_interviews');
    }
};
