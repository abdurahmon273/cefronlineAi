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
        Schema::create('writing_task_twos', function (Blueprint $table) {
            $table->id();
            $table->string('theme')->nullable();
            $table->string('requirement')->nullable();
            $table->text('advice')->nullable();
            $table->unsignedBigInteger('writing_exercise_id');

            $table->foreign('writing_exercise_id')->references('id')->on('writing_exercises');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('writing_task_twos');
    }
};
