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
        Schema::table('speaking_results', function (Blueprint $table) {
            $table->integer('process_category_type')->after('category_type')->default(0);
        });

        \App\Models\SpeakingResult::where('with_ai', 1)
            ->select('id', 'with_ai', 'status', 'category_type', 'process_category_type')
            ->chunkById(500, function ($speakingResults) {
                foreach ($speakingResults as $speakingResult) {
                    if ($speakingResult->status) {
                        $speakingResult->update([
                            'status' => 3,
                            'process_category_type' => intval($speakingResult->category_type),
                        ]);
                    } else {
                        $speakingResult->update(['status' => 4]);
                    }
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('speaking_results', function (Blueprint $table) {
            $table->dropColumn('process_category_type');
        });
    }
};
