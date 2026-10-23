<?php

namespace App\Http\Resources\Speaking;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpeakingExerciseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->part_one_first && $this->part_two && $this->part_three){
            return [
                'id'=>$this->id,
                'title'=>$this->title,
                'part_category_number'=>$this->part_category,
                'status'=>$this->status,
                'time_limit'=>$this->part_one_first->questions->sum('time')+$this->part_one_second->questions->sum('time')+$this->part_two->time+$this->part_three->time ?? null,
            ];
        }elseif ($this->part_one_first){
            return [
                'id'=>$this->id,
                'title'=>$this->title,
                'part_category_number'=>$this->part_category,
                'status'=>$this->status,
                'time_limit'=>$this->part_one_first->questions->sum('time') ?? 90,
            ];
        }elseif ($this->part_two){
            return [
                'id'=>$this->id,
                'title'=>$this->title,
                'part_category_number'=>$this->part_category,
                'status'=>$this->status,
                'time_limit'=>$this->part_two->time_limit ?? 120,
            ];
        }elseif ($this->part_three){
            return [
                'id'=>$this->id,
                'title'=>$this->title,
                'part_category_number'=>$this->part_category,
                'status'=>$this->status,
                'time_limit'=>$this->part_three->time_limit ?? 120,
            ];
        }else{
            return [];
        }
    }
}
