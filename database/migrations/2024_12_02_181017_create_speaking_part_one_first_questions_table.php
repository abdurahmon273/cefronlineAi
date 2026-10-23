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
        Schema::create('speaking_part_one_first_questions', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->bigInteger('time');
            $table->unsignedBigInteger('part_one_first_id');

            $table->foreign('part_one_first_id')->references('id')->on('speaking_part_one_firsts');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('speaking_part_one_first_questions');
    }
};
