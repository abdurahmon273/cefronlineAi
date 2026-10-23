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
        Schema::create('writing_results', function (Blueprint $table) {
            $table->id();
            $table->text('text_1')->nullable();
            $table->text('text_2')->nullable();
            $table->string('category_type');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('writing_exercise_id')->nullable();

            $table->foreign('writing_exercise_id')->references('id')->on('writing_exercises');
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
        Schema::dropIfExists('writing_results');
    }
};
