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
            $table->longText('theme')->nullable()->change();
        });
        Schema::table('writing_task_twos', function (Blueprint $table) {
            $table->longText('theme')->nullable()->change();
        });

        DB::statement("
            UPDATE writing_task_ones
            SET time_limit = 30
            WHERE time_limit IS NULL OR time_limit = '' OR time_limit REGEXP '[^0-9]'
        ");
        DB::statement("
            UPDATE writing_task_ones
            SET word_count = 200
            WHERE word_count IS NULL OR word_count = '' OR word_count REGEXP '[^0-9]'
        ");
        DB::statement("
            UPDATE writing_task_twos
            SET time_limit = 30
            WHERE time_limit IS NULL OR time_limit = '' OR time_limit REGEXP '[^0-9]'
        ");
        DB::statement("
            UPDATE writing_task_twos
            SET word_count = 200
            WHERE word_count IS NULL OR word_count = '' OR word_count REGEXP '[^0-9]'
        ");

        DB::statement("
            ALTER TABLE writing_task_ones
            MODIFY time_limit BIGINT NULL DEFAULT 30,
            MODIFY word_count BIGINT NULL DEFAULT 200
        ");
        DB::statement("
            ALTER TABLE writing_task_twos
            MODIFY time_limit BIGINT NULL DEFAULT 30,
            MODIFY word_count BIGINT NULL DEFAULT 200
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE writing_task_ones
            MODIFY time_limit BIGINT NULL DEFAULT NULL,
            MODIFY word_count BIGINT NULL DEFAULT NULL
        ");
        DB::statement("
            ALTER TABLE writing_task_twos
            MODIFY time_limit BIGINT NULL DEFAULT NULL,
            MODIFY word_count BIGINT NULL DEFAULT NULL
        ");

        Schema::table('writing_task_ones', function (Blueprint $table) {
            $table->string('theme')->nullable()->change();
        });
        Schema::table('writing_task_twos', function (Blueprint $table) {
            $table->string('theme')->nullable()->change();
        });
    }
};
