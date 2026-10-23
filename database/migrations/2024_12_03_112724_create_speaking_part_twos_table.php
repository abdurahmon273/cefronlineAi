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
        Schema::create('speaking_part_twos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->bigInteger('time');
            $table->unsignedBigInteger('speaking_exercise_id');

            $table->foreign('speaking_exercise_id')->references('id')->on('speaking_exercises');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('speaking_part_twos');
    }
};
