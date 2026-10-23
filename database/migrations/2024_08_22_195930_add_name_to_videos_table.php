<?php

use App\Models\Video;
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
        Schema::table('videos', function (Blueprint $table) {
            $table->string('name')->nullable();
        });
        $lessons = Video::select('lesson_id')->distinct()->pluck('lesson_id');

        if ($lessons->isNotEmpty()) {
            foreach ($lessons as $lessonId) {
                $videos = Video::where('lesson_id', $lessonId)->get();

                if ($videos->isNotEmpty()) {
                    foreach ($videos as $index => $video) {
                        $video->name = 'lesson ' . ($index + 1);
                        $video->save();
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
};
