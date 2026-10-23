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
        Schema::table('writing_part_one_answers', function (Blueprint $table) {
            $table->dropColumn('score');
            $table->decimal('score_1', 3, 1)->default(0);
            $table->decimal('score_2', 3, 1)->default(0);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('writing_part_one_answers', function (Blueprint $table) {

            $table->decimal('score', 3, 1)->default(0);
            $table->dropColumn('score_1');
            $table->dropColumn('score_2');

        });
    }
};
