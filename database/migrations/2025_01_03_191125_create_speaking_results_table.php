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
        Schema::create('speaking_results', function (Blueprint $table) {
            $table->id();
            $table->string('category_type');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('speaking_exercise_id')->nullable();

            $table->foreign('speaking_exercise_id')->references('id')->on('speaking_exercises');
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
        Schema::dropIfExists('speaking_results');
    }
};
