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
        Schema::create('applicant_references', function (Blueprint $table) {
            $table->id();

            $table->foreignId('applicant_id')->constrained('applicants')->cascadeOnDelete();
            $table->smallInteger('sequence')->default(1);
            $table->string('name', 100);
            $table->string('relation', 100)->nullable();
            $table->string('organization');
            $table->string('designation');
            $table->string('mobile', 20);
            $table->string('phone_office', 20)->nullable();
            $table->string('phone_home', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('address')->nullable();

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
        Schema::dropIfExists('applicant_references');
    }
};
