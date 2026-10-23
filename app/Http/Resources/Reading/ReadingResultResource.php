<?php

namespace App\Http\Resources\Reading;

use App\Models\ListeningResult;
use App\Models\ReadingResult;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class ReadingResultResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'all_answers'=>$this->all_answers,
            'wrong_answers'=>$this->wrong_answers,
            'correct_answers'=>$this->correct_answers,
            'percentage'=>$this->percent,
            'category'=> isset(ReadingResult::CATEGORY_TYPES[$this->category_type]) ? ReadingResult::CATEGORY_TYPES[$this->category_type] : null,
            'reading_exercise_id'=>$this->reading_exercise_id,
            'createdDate'=>  $this->created_at
        ];
    }
}
