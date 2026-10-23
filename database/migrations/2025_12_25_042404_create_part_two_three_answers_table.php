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
        Schema::create('part_two_three_answers', function (Blueprint $table) {
            $table->id();
            $table->integer('type')->nullable();
            $table->integer('is_correct')->nullable();
            $table->text('correct_answer')->nullable();

            $table->unsignedBigInteger('reading_result_id');
            $table->unsignedBigInteger('answer_id')->nullable();
            $table->unsignedBigInteger('correct_answer_id');


            $table->foreign('reading_result_id')->references('id')->on('reading_results')->onDelete('cascade');
            $table->foreign('answer_id')->references('id')->on('reading_part_two_three_texts')->onDelete('cascade');
            $table->foreign('correct_answer_id')->references('id')->on('reading_part_two_three_answers')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('part_two_three_answers');
    }
};
