<?php

namespace App\Http\Resources\V2\Full;

use App\Enums\CefrTypeEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class FullResultListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $listeningScore = $this->listening_result
            ? (CefrTypeEnum::LISTENING_BALL[$this->listening_result->correct_answers] ?? 0)
            : 0;

        $readingScore = $this->reading_result
            ? (CefrTypeEnum::READING_BALL[$this->reading_result->correct_answers] ?? 0)
            : 0;

        $writingScore = $this->writing_result
            ? (CefrTypeEnum::WRITING_BALL[3][$this->normalizeBand($this->writing_result->score)] ?? 0)
            : 0;

        $speakingScore = $this->speaking_result
            ? (CefrTypeEnum::SPEAKING_BALL[4][$this->normalizeBand($this->speaking_result->score)] ?? 0)
            : 0;

        // overall = average of only the skills that were actually taken
        $takenScores = [];
        if ($this->listening_result) $takenScores[] = $listeningScore;
        if ($this->reading_result)   $takenScores[] = $readingScore;
        if ($this->writing_result)   $takenScores[] = $writingScore;
        if ($this->speaking_result)  $takenScores[] = $speakingScore;

        $overall = count($takenScores) > 0
            ? (int) round(array_sum($takenScores) / count($takenScores))
            : 0;

        return [
            'id'               => $this->id,
            'full_exercise_id' => $this->full_exercise_id,
            'name'             => $this->exercise?->title,
            'date'             => $this->date?->format('Y-m-d H:i'),
            'score'            => $overall,
            'listening_score'  => $listeningScore,
            'reading_score'    => $readingScore,
            'writing_score'    => $writingScore,
            'speaking_score'   => $speakingScore,
            'createdDate'=>  $this->date
        ];
    }

    /**
     * Normalize a numeric band (e.g. 4, 4.0, 4.50) into the "4" / "4.5" key
     * format used by the CefrTypeEnum WRITING_BALL / SPEAKING_BALL lookup tables.
     */
    private function normalizeBand($score): string
    {
        return rtrim(rtrim(number_format((float) $score, 1, '.', ''), '0'), '.');
    }
}
