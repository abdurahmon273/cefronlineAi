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
        Schema::create('writing_part_one_answers', function (Blueprint $table) {
            $table->id();
            $table->longText('text_1')->nullable();
            $table->longText('text_2')->nullable();
            $table->unsignedBigInteger('writing_result_id');
            $table->integer('score')->default(0);
            $table->integer('max_score')->default(0);

            $table->foreign('writing_result_id')->references('id')->on('writing_results')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('writing_part_one_answers');
    }
};
