<?php

namespace App\Http\Resources\V2\Listening;

use App\Models\ListeningExercise;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListeningExerciseResource extends JsonResource
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
            'exercise_id'=>$this->exercise_id,
            'status'=>$this->status,
        ];
    }
}
