<?php

namespace App\Http\Resources\Writing;

use App\Models\ReadingExercise;
use App\Models\WritingExercise;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WritingExerciseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        if ($this->task_one && $this->task_two){
            return [
                'id'=>$this->id,
                'name'=>$this->name,
                'part_category_number'=>$this->part_category,
                'status'=>$this->status,
                'time_limit'=>$this->task_one->time_limit+$this->task_two->time_limit ?? null,
                'word_count'=>$this->word_count ?? null,
            ];
        }elseif ($this->task_one){
            return [
                'id'=>$this->id,
                'name'=>$this->name,
                'part_category_number'=>$this->part_category,
                'status'=>$this->status,
                'time_limit'=>$this->task_one->time_limit ?? 20,
                'word_count'=>$this->task_one->word_count ?? 150,
            ];
        }elseif ($this->task_two){
            return [
                'id'=>$this->id,
                'name'=>$this->name,
                'part_category_number'=>$this->part_category,
                'status'=>$this->status,
                'time_limit'=>$this->task_two->time_limit ?? 40,
                'word_count'=>$this->task_two->word_count ?? 150,
            ];
        }else{
            return [];
        }

    }
}
