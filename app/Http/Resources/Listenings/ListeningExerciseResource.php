<?php

namespace App\Http\Resources\Listenings;

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
            'part_category'=>ListeningExercise::PART_CATEGORIES[$this->part_category],
            'part_category_number'=>$this->part_category,
            'status'=>$this->status,
            'difficulty'=>$this->difficulty,
            'time_limit'=>$this->time_limit ?? 6,
            'exercise_id'=>$this->exercise_id,
        ];
    }
}
