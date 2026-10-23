<?php

namespace App\Http\Resources\V2\Reading;

use App\Models\ListeningExercise;
use App\Models\ReadingExercise;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReadingExercisesResource extends JsonResource
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
            'name'=>$this->name,
            'part_category_number'=>$this->part_category,
            'status'=>$this->status,
            'exercise_id'=>$this->exercise_id
        ];
    }
}
