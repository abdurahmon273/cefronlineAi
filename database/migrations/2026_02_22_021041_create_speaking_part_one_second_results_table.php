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
        Schema::create('speaking_part_one_second_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('speaking_result_id');
            $table->decimal('score', 8, 2)->nullable();
            $table->json('result_json')->nullable();
            $table->foreign('speaking_result_id')->references('id')->on('speaking_results');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('speaking_part_one_second_results');
    }
};
