<?php

namespace App\Http\Resources\V2\Writing;

use App\Models\WritingResult;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class WritingResultResource extends JsonResource
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
            'category_type'=>$this->category_type,
            'category'=> isset(WritingResult::CATEGORY_TYPES[$this->category_type]) ? WritingResult::CATEGORY_TYPES[$this->category_type] : null,
            'writing_exercise_id'=>$this->writing_exercise_id,
            'date'=>$this->created_at,
            'percentage'=>$this->percentageData['percent'],
            'ball'=>intval($this->score),
            'score'=>str( $this->percentageData['ball']),
            'max_score'=>$this->max_score,
            'createdDate'=>  $this->created_at,
        ];
    }
}
