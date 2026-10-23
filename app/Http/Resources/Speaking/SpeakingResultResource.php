<?php

namespace App\Http\Resources\Speaking;

use App\Models\SpeakingResult;
use App\Models\WritingResult;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'audio_path'=>$this->audio_path,
            'category_type'=>$this->category_type,
            'category'=> isset(SpeakingResult::CATEGORY_TYPES[$this->category_type]) ? SpeakingResult::CATEGORY_TYPES[$this->category_type] : null,
            'Speaking_exercise_id'=>$this->speaking_exercise_id,
            'created_at'=>$this->created_at,
        ];
    }
}
