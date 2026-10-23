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
        Schema::create('full_exercises', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('status');
            $table->unsignedBigInteger('exercise_id');

            $table->foreign('exercise_id')->references('id')->on('exercises');

            $table->unsignedBigInteger('listening_exercise_id')->nullable();
            $table->foreign('listening_exercise_id')->references('id')->on('listening_exercises');
            $table->unsignedBigInteger('reading_exercise_id')->nullable();
            $table->foreign('reading_exercise_id')->references('id')->on('reading_exercises');
            $table->unsignedBigInteger('writing_exercise_id')->nullable();
            $table->foreign('writing_exercise_id')->references('id')->on('writing_exercises');
            $table->unsignedBigInteger('speaking_exercise_id')->nullable();
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
        Schema::dropIfExists('full_exercises');
    }
};
