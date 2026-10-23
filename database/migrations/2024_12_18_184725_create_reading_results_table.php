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
        Schema::create('reading_results', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('all_answers');
            $table->bigInteger('correct_answers');
            $table->bigInteger('percent');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('reading_exercise_id');

            $table->foreign('reading_exercise_id')->references('id')->on('reading_exercises');
            $table->foreign('user_id')->references('id')->on('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reading_results');
    }
};
