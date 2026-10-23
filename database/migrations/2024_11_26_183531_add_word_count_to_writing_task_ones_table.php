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
        Schema::table('writing_task_ones', function (Blueprint $table) {
            $table->bigInteger('time_limit')->nullable();
            $table->bigInteger('word_count')->nullable();
            $table->text('bullet_point_1')->nullable();
            $table->text('bullet_point_2')->nullable();
            $table->text('bullet_point_3')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('writing_task_ones', function (Blueprint $table) {
            //
        });
    }
};
