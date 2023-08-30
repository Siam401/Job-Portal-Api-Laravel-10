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
        Schema::create('applicant_experiences', function (Blueprint $table) {
            $table->id();

            $table->foreignId('applicant_id')->constrained('applicants')->cascadeOnDelete();
            $table->string('company_name');
            $table->string('company_business')->nullable();
            $table->string('designation', 100);
            $table->string('department', 100)->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->default(false);
            $table->string('location')->nullable();
            $table->text('responsibility')->nullable();
            $table->text('expertise')->nullable()->comment('Json data array: [skill-name, year, month]');

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
        Schema::dropIfExists('applicant_experiences');
    }
};
