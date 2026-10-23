<?php

namespace App\Http\Resources\V2\Speaking;

use App\Models\SpeakingResult;
use App\Models\WritingResult;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class SpeakingResultResource extends JsonResource
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
            'Speaking_exercise_id'=>$this->speaking_exercise_id,
            'category_type'=>$this->category_type,
            'category'=> isset(SpeakingResult::CATEGORY_TYPES[$this->category_type]) ? SpeakingResult::CATEGORY_TYPES[$this->category_type] : null,
            'percentage_data'=>$this->percentageData,
            'max_score'=>$this->max_score,
            'createdDate'=>  $this->created_at
        ];
    }
}
