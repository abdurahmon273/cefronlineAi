<?php

namespace App\Jobs;

use App\Models\SpeakingResult;
use App\Services\DeepgramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class SpeakingChangeAudioFormat implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $speakingResultId)
    {
    }

    public function handle(DeepgramService $deepgram): void
    {
        $result = SpeakingResult::with([
            'part_one_first.answers',
            'part_one_second.answers',
            'part_two',
            'part_three',
            'exercise',
        ])->find($this->speakingResultId);

        if (!$result || !$result->exercise) {
            return;
        }

        $category = (int) $result->exercise->part_category;

        if ($category === 1 || $category === 4) {
            foreach ($result->part_one_first?->answers ?? [] as $answer) {
                $this->toMp3($deepgram, $answer);
            }
            foreach ($result->part_one_second?->answers ?? [] as $answer) {
                $this->toMp3($deepgram, $answer);
            }
        }

        if ($category === 2 || $category === 4) {
            $this->toMp3($deepgram, $result->part_two);
        }

        if ($category === 3 || $category === 4) {
            $this->toMp3($deepgram, $result->part_three);
        }
    }

    private function toMp3(DeepgramService $deepgram, $row): void
    {
        try {
            if (!$row || !$row->audio_path) {
                return;
            }

            $oldPath = $row->audio_path;
            $newPath = $deepgram->generate_mp3($oldPath);

            if ($newPath && $newPath !== $oldPath) {
                // 1) AVVAL DB ni yangilaymiz — endi audio_path mp3 ga ishora qiladi
                $row->update(['audio_path' => $newPath]);

                // 2) KEYIN eski webm ni o'chiramiz. Update'gacha o'chirmaganimiz uchun
                //    job yarmda uzilib/retry bo'lsa ham fayl yo'qolmaydi, DB nomuvofiq qolmaydi.
                Storage::disk('private')->delete("voiceAudios/{$oldPath}");
            }
        } catch (\Throwable $e) {
        }
    }
}
