<?php

namespace App\Http\Resources\Writing;

use App\Models\WritingResult;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'text_1'=>$this->text_1,
            'text_2'=>$this->text_2,
            'category_type'=>$this->category_type,
            'category'=> isset(WritingResult::CATEGORY_TYPES[$this->category_type]) ? WritingResult::CATEGORY_TYPES[$this->category_type] : null,
            'writing_exercise_id'=>$this->writing_exercise_id,
            'date'=>$this->created_at
        ];
    }
}
