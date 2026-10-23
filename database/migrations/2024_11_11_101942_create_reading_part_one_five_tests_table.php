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
        Schema::create('reading_part_one_five_tests', function (Blueprint $table) {
            $table->id();
            $table->string('count_number'); //1,2,3,4,5,6
            $table->string('correct_answer');

            $table->unsignedBigInteger('reading_part_one_five_question_id');

            $table->foreign('reading_part_one_five_question_id')
                ->references('id')
                ->on('reading_part_one_five_questions')
                ->name('rp1f_tests_rp1f_question_id_foreign'); // Shortened name
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reading_part_one_five_tests');
    }
};
