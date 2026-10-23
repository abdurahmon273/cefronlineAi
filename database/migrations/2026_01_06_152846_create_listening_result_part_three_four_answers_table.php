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
        Schema::create('listening_result_part_three_four_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('listening_result_id');
            $table->integer('type');
            $table->integer('is_correct');
            $table->unsignedBigInteger('answer_id');
            $table->text('correct_answer');
            $table->unsignedBigInteger('correct_answer_id');


            $table->foreign('answer_id', 'lr_pt34_answer_fk')->references('id')->on('listening_part_three_four_answers');
            $table->foreign('correct_answer_id', 'lr_pt34_correct_fk')->references('id')->on('listening_part_three_four_answers');
            $table->foreign('listening_result_id', 'lr_pt34_result_fk')->references('id')->on('listening_results');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listening_result_part_three_four_answers');
    }
};
