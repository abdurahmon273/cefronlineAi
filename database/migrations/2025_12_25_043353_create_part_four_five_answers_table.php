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
        Schema::create('part_four_five_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reading_result_id');
            $table->foreign('reading_result_id')->references('id')->on('reading_results')->onDelete('cascade');
            $table->integer('type')->nullable();
            $table->integer('is_correct')->nullable();
            $table->string('answer_key')->nullable();
            $table->string('correct_answer_key')->nullable();
            $table->text('correct_answer')->nullable();
            $table->unsignedBigInteger('test_id');
            $table->foreign('test_id')->references('id')->on('reading_part_four_tests')->onDelete('cascade');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('part_four_five_answers');
    }
};
