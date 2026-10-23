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
        Schema::create('speaking_part_one_first_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('one_first_result_id');
            $table->unsignedBigInteger('question_id');
            $table->string('audio_path')->nullable();
            $table->text('audio_text')->nullable();

            $table->foreign('one_first_result_id')->references('id')->on('speaking_part_one_first_results');
            $table->foreign('question_id')->references('id')->on('speaking_part_one_first_questions');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('speaking_part_one_first_answers');
    }
};
