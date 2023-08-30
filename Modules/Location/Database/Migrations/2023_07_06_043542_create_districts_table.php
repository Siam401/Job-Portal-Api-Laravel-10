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
        Schema::create('districts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('division_id')->constrained('divisions');
            $table->string('name', 100);
            $table->decimal('latitude', 10, 8)->default(0.0);
            $table->decimal('longitude', 11, 8)->default(0.0);

            $table->unique(['division_id', 'name']);
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
        Schema::dropIfExists('districts');
    }
};
