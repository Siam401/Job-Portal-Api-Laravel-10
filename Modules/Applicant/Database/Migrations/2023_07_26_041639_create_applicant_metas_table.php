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
        Schema::create('applicant_metas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('applicant_id')->constrained('applicants')->cascadeOnDelete();
            $table->string('key_name', 100);
            $table->mediumText('key_value')->nullable();

            $table->unique(['applicant_id', 'key_name'], 'applicant_key_name_unique');

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
        Schema::dropIfExists('applicant_metas');
    }
};
