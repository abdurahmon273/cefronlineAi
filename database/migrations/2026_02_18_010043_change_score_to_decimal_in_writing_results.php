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
        Schema::table('writing_results', function (Blueprint $table) {
            $table->decimal('score', 3, 1)->default(0)->change();
        });

        Schema::table('writing_part_one_answers', function (Blueprint $table) {
            $table->decimal('score', 3, 1)->default(0)->change();
        });

        Schema::table('writing_part_two_answers', function (Blueprint $table) {
            $table->decimal('score', 3, 1)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('writing_results', function (Blueprint $table) {
            $table->integer('score')->default(0)->change();
        });

        Schema::table('writing_part_one_answers', function (Blueprint $table) {
            $table->integer('score')->default(0)->change();
        });

        Schema::table('writing_part_two_answers', function (Blueprint $table) {
            $table->integer('score')->default(0)->change();
        });
    }
};
