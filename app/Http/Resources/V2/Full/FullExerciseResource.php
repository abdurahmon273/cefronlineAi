<?php

namespace App\Http\Resources\V2\Full;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FullExerciseResource extends JsonResource
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
            'title'=>$this->title,
            'status'=>$this->status,
            'time_limit'=> ($this->listening_exercise_id ?  35 : 0)+
                ($this->reading_exercise_id ?  70 : 0)+
                ($this->writing_exercise_id ?  60 : 0 )+
                ($this->speaking_exercise_id ?  10 : 0 ),
        ];
    }
}
