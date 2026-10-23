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
        Schema::create('reading_part_four_tests', function (Blueprint $table) {
            $table->id();
            $table->text('question')->nullable();
            $table->string('answer_1');
            $table->string('answer_2');
            $table->string('answer_3');
            $table->string('answer_4');
            $table->string('correct_answer'); // [1,2,3,4]
            $table->string('type'); // [0=>'true false',1=>'question']

            $table->unsignedBigInteger('reading_part_four_id');

            $table->foreign('reading_part_four_id')
                ->references('id')
                ->on('reading_part_fours')
                ->name('rp1f_tests_rp1f_four_id_foreign'); // Shortened name
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reading_part_four_tests');
    }
};
