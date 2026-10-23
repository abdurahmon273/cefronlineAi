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
            $table->json('result_1_json')->after('score')->nullable();
            $table->json('result_2_json')->after('result_1_json')->nullable();
        });
        Schema::table('writing_part_two_answers', function (Blueprint $table) {
            $table->json('result_json')->after('score')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('writing_part_one_answers', function (Blueprint $table) {
            $table->dropColumn('result_1_json');
            $table->dropColumn('result_2_json');
        });
        Schema::table('writing_part_two_answers', function (Blueprint $table) {
            $table->dropColumn('result_json');
        });
    }
};
