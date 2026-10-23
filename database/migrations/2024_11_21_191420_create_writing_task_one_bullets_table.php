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
        Schema::create('writing_task_one_bullets', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->unsignedBigInteger('writing_task_one_id');

            $table->foreign('writing_task_one_id')->references('id')->on('writing_task_ones');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('writing_task_one_bullets');
    }
};
