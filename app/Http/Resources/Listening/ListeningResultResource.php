<?php

namespace App\Http\Resources\Listening;

use App\Models\ListeningResult;
use App\Models\WritingResult;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListeningResultResource extends JsonResource
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
            'correct_answers'=>$this->correct_answers,
            'percentage'=>$this->percent,
            'category'=> isset(ListeningResult::CATEGORY_TYPES[$this->category_type]) ? ListeningResult::CATEGORY_TYPES[$this->category_type] : null,
            'listening_exercise_id'=>$this->listening_exercise_id,

        ];
    }
}
