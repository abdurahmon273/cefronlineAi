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
        Schema::create('listening_part_three_four_answers', function (Blueprint $table) {
            $table->id();
            $table->string('letter');
            $table->text('answer');

            $table->unsignedBigInteger('listening_part_three_four_id');
            $table->foreign('listening_part_three_four_id')
                ->references('id')
                ->on('listening_part_three_fours')
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
        Schema::dropIfExists('listening_part_three_four_answers');
    }
};
