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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('code', 32)->unique();
            $table->tinyInteger('level')->default(1)->comment('1: company  2: wing  3: branch');
            $table->unsignedBigInteger('parent_id')->default(0)->comment('Self join parent ID');
            $table->string('address')->nullable();
            $table->string('city', 50)->nullable();
            $table->foreignId('district_id');
            $table->unsignedBigInteger('area_id')->nullable();
            $table->string('zipcode', 20)->nullable();
            $table->string('logo')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->unique();
            $table->string('from_name', 100)->nullable()->comment('Formal name of Company email sender');
            $table->string('reg_number', 50)->nullable()->comment('Company registration number');
            $table->string('tax_type', 10)->nullable()->comment('Values: vat, gst');
            $table->string('tax_number', 100)->nullable()->comment('Tax Identification Number (TIN)');
            $table->string('timezone', 50)->default('Asia/Dhaka');
            $table->time('office_start_time')->nullable();
            $table->time('office_end_time')->nullable();
            $table->string('website')->nullable()->comment('Website link address');
            $table->string('weekends', 20)->nullable()->comment('Comma separated parameters of day number');

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
        Schema::dropIfExists('companies');
    }
};
