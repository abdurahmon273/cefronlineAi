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
        Schema::table('speaking_part_one_first_questions', function (Blueprint $table) {
            $table->string('audio_url')->nullable();
        });
        Schema::table('speaking_part_one_second_questions', function (Blueprint $table) {
            $table->string('audio_url')->nullable();
        });
        Schema::table('speaking_part_two_questions', function (Blueprint $table) {
            $table->string('audio_url')->nullable();
        });
        Schema::table('speaking_part_threes', function (Blueprint $table) {
            $table->string('theme_audio_url')->nullable();
            $table->string('audio_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('speaking_part_one_first_questions', function (Blueprint $table) {
            $table->dropColumn('audio_url');
        });
        Schema::table('speaking_part_one_second_questions', function (Blueprint $table) {
            $table->dropColumn('audio_url');
        });
        Schema::table('speaking_part_two_questions', function (Blueprint $table) {
            $table->dropColumn('audio_url');
        });
        Schema::table('speaking_part_threes', function (Blueprint $table) {
            $table->dropColumn('theme_audio_url');
            $table->dropColumn('audio_url');
        });
    }
};
