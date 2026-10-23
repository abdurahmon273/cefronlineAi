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
        Schema::create('listening_part_one_fife_tests', function (Blueprint $table) {
            $table->id();
            $table->text('question')->nullable();
            $table->string('answer_1');
            $table->string('answer_2');
            $table->string('answer_3');
            $table->string('correct_answer'); // [0,1,2]

            $table->unsignedBigInteger('listening_part_one_fife_question_id');

            $table->foreign('listening_part_one_fife_question_id')
                ->references('id')
                ->on('listening_part_one_fife_questions')
                ->name('lp1f_tests_lp1f_question_id_foreign'); // Shortened name
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listening_part_one_fife_tests');
    }
};
