<?php

namespace App\Http\Resources\V2\Writing;

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
            ];
        }elseif ($this->task_one){
            return [
                'id'=>$this->id,
                'name'=>$this->name,
                'part_category_number'=>$this->part_category,
                'status'=>$this->status,
            ];
        }elseif ($this->task_two){
            return [
                'id'=>$this->id,
                'name'=>$this->name,
                'part_category_number'=>$this->part_category,
                'status'=>$this->status,
            ];
        }else{
            return [];
        }

    }
}
