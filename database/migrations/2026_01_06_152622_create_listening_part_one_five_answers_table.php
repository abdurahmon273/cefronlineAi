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
        Schema::create('listening_part_one_five_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('listening_result_id');
            $table->integer('type');
            $table->integer('is_correct');
            $table->string('answer_key');
            $table->string('correct_answer_key');
            $table->text('correct_answer');
            $table->unsignedBigInteger('test_id');

            $table->foreign('test_id')->references('id')->on('listening_part_one_fife_tests');
            $table->foreign('listening_result_id')->references('id')->on('listening_results');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listening_part_one_five_answers');
    }
};
