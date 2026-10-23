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
        Schema::create('full_results', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->default(0);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('full_exercise_id');
            $table->unsignedBigInteger('listening_result_id')->nullable();
            $table->unsignedBigInteger('reading_result_id')->nullable();
            $table->unsignedBigInteger('writing_result_id')->nullable();
            $table->unsignedBigInteger('speaking_result_id')->nullable();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('full_exercise_id')->references('id')->on('full_exercises');
            $table->foreign('listening_result_id')->references('id')->on('listening_results');
            $table->foreign('reading_result_id')->references('id')->on('reading_results');
            $table->foreign('writing_result_id')->references('id')->on('writing_results');
            $table->foreign('speaking_result_id')->references('id')->on('speaking_results');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('full_results');
    }
};
