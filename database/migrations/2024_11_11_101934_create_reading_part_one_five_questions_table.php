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
        Schema::create('reading_part_one_five_questions', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('reading_text')->nullable();
            $table->text('question_text')->nullable();
            $table->text('advice')->nullable();
            $table->string('type');
            $table->unsignedBigInteger('reading_exercise_id');

            $table->foreign('reading_exercise_id')->references('id')->on('reading_exercises');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reading_part_one_five_questions');
    }
};
