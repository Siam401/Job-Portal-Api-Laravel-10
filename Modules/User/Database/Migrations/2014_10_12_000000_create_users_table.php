<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('user_type', 12)->default('employee')->comment('Values: admin, employee, applicant');
            $table->string('name', 100);
            $table->string('reg_type', 12)->default('unknown')->comment('Values: unknown, email, mobile, facebook, twitter etc.');
            $table->string('email', 100)->unique()->nullable()->comment('Account username will be checked between email, mobile & social account ID');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('country_code', 6)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->timestamp('mobile_verified_at')->nullable();
            $table->string('password');
            $table->string('photo')->nullable();
            $table->tinyInteger('status')->default(0)->comment('1: Active, 0: In-Active, 2: Blocked');
            $table->string('v_code', 10)->nullable();
            $table->timestamp('v_code_send_at')->nullable();
            $table->rememberToken();

            $table->unique(['country_code', 'mobile'], 'phone');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
