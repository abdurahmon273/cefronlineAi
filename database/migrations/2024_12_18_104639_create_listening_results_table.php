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
        Schema::create('listening_results', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('all_answers');
            $table->bigInteger('correct_answers');
            $table->float('percent');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('listening_exercise_id');

            $table->foreign('listening_exercise_id')->references('id')->on('listening_exercises');
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
        Schema::dropIfExists('listening_results');
    }
};
