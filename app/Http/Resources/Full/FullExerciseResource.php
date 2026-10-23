<?php

namespace App\Http\Resources\Full;

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
            'time_limit'=>
                ($this->listening_exercise->time_limit ?? 35)+
                ($this->reading_exercise->time_limit ?? 70)+
                (($this->writing_exercise->task_one ? $this->writing_exercise->task_one->time_limit  : 20 )+
                ($this->writing_exercise->task_two? $this->writing_exercise->task_two->time_limit : 40))+
                ((($this->speaking_exercise->part_one_first ? $this->speaking_exercise->part_one_first->questions->sum('time') : 120 )/60)+
                (($this->speaking_exercise->part_one_second? $this->speaking_exercise->part_one_second->questions->sum('time') : 120)/60)+
                (($this->speaking_exercise->part_two? $this->speaking_exercise->part_two->time : 120)/60)+
                (($this->speaking_exercise->part_three ? $this->speaking_exercise->part_three->time : 120))/60) ?? null,
        ];
    }
}
