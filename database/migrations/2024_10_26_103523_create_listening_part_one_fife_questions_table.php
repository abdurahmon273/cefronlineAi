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
        Schema::create('listening_part_one_fife_questions', function (Blueprint $table) {
            $table->id();
            $table->string('audio');
            $table->text('advice');
            $table->string('type'); //['0'=>'part 1','1'=>'part 5']

            $table->unsignedBigInteger('listening_exercise_id');
            $table->foreign('listening_exercise_id')->references('id')->on('listening_exercises');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listening_part_one_fife_question');
    }
};
