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
        Schema::create('part_one_five_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reading_result_id');
            $table->foreign('reading_result_id')->references('id')->on('reading_results')->onDelete('cascade');
            $table->integer('type')->nullable();
            $table->integer('is_correct')->nullable();
            $table->string('answer')->nullable();
            $table->string('correct_answer')->nullable();
            $table->unsignedBigInteger('answer_id')->nullable();
            $table->foreign('answer_id')->references('id')->on('reading_part_one_five_tests')->onDelete('cascade');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('part_one_five_answers');
    }
};
