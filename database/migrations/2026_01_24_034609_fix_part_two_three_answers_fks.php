<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('part_two_three_answers', function (Blueprint $table) {

            // 1) Old FK: answer_id -> reading_part_two_three_texts (WRONG)
            // Drop foreign key first
            try {
                $table->dropForeign(['answer_id']);
            } catch (\Throwable $e) {
                // Agar constraint nomi custom bo'lsa, bu yerda ignore qilamiz.
                // Kerak bo'lsa: $table->dropForeign('constraint_name');
            }

            // 2) Add new text_id column (nullable agar eski data bo'lsa)
            if (!Schema::hasColumn('part_two_three_answers', 'text_id')) {
                $table->unsignedBigInteger('text_id')->nullable()->after('answer_id');
            }

            // 3) Fix answer_id FK -> reading_part_two_three_answers
            $table->foreign('answer_id')
                ->references('id')
                ->on('reading_part_two_three_answers')
                ->onDelete('cascade');

            // 4) Add text_id FK -> reading_part_two_three_texts
            $table->foreign('text_id')
                ->references('id')
                ->on('reading_part_two_three_texts')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('part_two_three_answers', function (Blueprint $table) {

            // Drop new FK
            try { $table->dropForeign(['text_id']); } catch (\Throwable $e) {}
            try { $table->dropForeign(['answer_id']); } catch (\Throwable $e) {}

            // Drop column
            if (Schema::hasColumn('part_two_three_answers', 'text_id')) {
                $table->dropColumn('text_id');
            }

            // Revert answer_id FK back to old (WRONG) reference (as it was)
            $table->foreign('answer_id')
                ->references('id')
                ->on('reading_part_two_three_texts')
                ->onDelete('cascade');
        });
    }
};
