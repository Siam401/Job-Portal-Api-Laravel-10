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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();

            $table->string('category', 20)->default('default')->comment('Values: default, ngo_care');
            $table->string('title');
            $table->unsignedBigInteger('wing_id')->default(0);
            $table->unsignedBigInteger('branch_id')->default(0);
            $table->foreignId('job_function_id');
            $table->string('vacancy', 20)->nullable();
            $table->string('code', 20)->unique();
            $table->tinyInteger('status')->default(0)->comment('0: Inactive, 1: Active, 2: Expired 3: Closed');
            $table->date('start_date');
            $table->date('end_date');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jobs');
    }
};
