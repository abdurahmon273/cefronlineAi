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
        Schema::create('reading_part_two_three_answers', function (Blueprint $table) {
            $table->id();
            $table->string('letter');
            $table->text('answer');
            $table->unsignedBigInteger('reading_part_two_three_id');

            $table->foreign('reading_part_two_three_id')
                ->references('id')
                ->on('reading_part_two_threes')
                ->name('fk_three_four_id'); // Specify a shorter name here

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reading_part_two_three_answers');
    }
};
