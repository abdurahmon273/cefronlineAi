<?php

use App\Models\SpeakingPartOneFirstAnswer;
use App\Models\SpeakingPartOneSecondAnswer;
use App\Models\SpeakingPartThreeResult;
use App\Models\SpeakingPartTwoResult;
use App\Services\DeepgramService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /**
     * audio_path mavjud bo'lgan barcha speaking answer/result larni aylanib chiqadi:
     *  - 1 oydan ESKI (created_at < 1 oy oldin): audio faylni storage'dan o'chiradi,
     *    audio_path'ni null qiladi.
     *  - 1 oy ICHIDA: audioni mp3 ga o'giradi (try/catch — ishlamasa ham muammo yo'q).
     *
     * Eslatma: audio_path null bo'lgan qatorlar ilova kodida allaqachon e'tiborga
     * olinmaydi (hamma joyda `if ($x->audio_path)` tekshiruvi bor).
     */
    public function up(): void
    {
        $cutoff   = now()->subMonth();
        $disk     = Storage::disk('private');
        $deepgram = app(DeepgramService::class);

        $models = [
            SpeakingPartOneFirstAnswer::class,
            SpeakingPartOneSecondAnswer::class,
            SpeakingPartTwoResult::class,
            SpeakingPartThreeResult::class,
        ];

        foreach ($models as $model) {
            $model::query()
                ->whereNotNull('audio_path')
                ->where('audio_path', '!=', '')
                ->chunkById(200, function ($rows) use ($cutoff, $disk, $deepgram) {
                    foreach ($rows as $row) {

                        // --- 1 oydan eski: faylni o'chir + audio_path = null ---
                        if ($row->created_at && $row->created_at->lt($cutoff)) {
                            try {
                                $disk->delete('voiceAudios/' . $row->audio_path);
                            } catch (\Throwable $e) {
                                // fayl bo'lmasa/o'chmasa ham davom
                            }

                            $row->update(['audio_path' => null]);
                            continue;
                        }

                        // --- 1 oy ichida: mp3 ga o'gir (xato bo'lsa ham muammo yo'q) ---
                        try {
                            $newPath = $deepgram->generate_mp3($row->audio_path);

                            if ($newPath && $newPath !== $row->audio_path) {
                                $row->update(['audio_path' => $newPath]);
                            }
                        } catch (\Throwable $e) {
                            // mp3 ga o'girish ishlamasa — eski (webm) holicha qoladi
                        }
                    }
                });
        }
    }

    /**
     * O'chirilgan audio fayllar va null qilingan yo'llarni tiklab bo'lmaydi.
     */
    public function down(): void
    {
        //
    }
};
