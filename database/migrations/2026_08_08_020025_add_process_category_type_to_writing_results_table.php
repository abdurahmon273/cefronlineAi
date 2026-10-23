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
            $table->integer('process_category_type')->after('category_type')->default(0);
        });

        \App\Models\WritingResult::where('with_ai', 1)
            ->select('id', 'with_ai', 'status', 'category_type', 'process_category_type')
            ->chunkById(500, function ($writingResults) {
                foreach ($writingResults as $writingResult) {
                    if ($writingResult->status) {
                        $writingResult->update([
                            'status' => 3,
                            'process_category_type' => intval($writingResult->category_type),
                        ]);
                    } else {
                        $writingResult->update(['status' => 4]);
                    }
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('writing_results', function (Blueprint $table) {
            $table->dropColumn('process_category_type');
        });
    }
};
