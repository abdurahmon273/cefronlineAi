<?php

namespace App\Http\Resources\Writing;

use App\Http\Resources\Reading\ReadingPartFiveResource;
use App\Http\Resources\Reading\ReadingPartFourResource;
use App\Http\Resources\Reading\ReadingPartOneResource;
use App\Http\Resources\Reading\ReadingPartTwoResource;
use App\Models\ReadingExercise;
use App\Models\WritingExercise;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WritingExerciseWithPartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $part_category='';
        if (isset(WritingExercise::PART_CATEGORIES[$this->part_category])){
            $part_category=WritingExercise::PART_CATEGORIES[$this->part_category];
        }

        if ($this->exercise->part_type=='1'){
            return [
                'id'=>$this->id,
                'name'=>$this->name,
                'part_category'=>$part_category,
                'part_category_number'=>$this->part_category,
                'status'=>$this->status,
                'time_limit'=>$this->task_one->time_limit ?? 20,
                'question'=>new WritingTaskOneResource($this->task_one ) ?? [],
            ];
        }elseif ($this->exercise->part_type=='2'){
            return [
                'id'=>$this->id,
                'name'=>$this->name,
                'part_category'=>$part_category,
                'part_category_number'=>$this->part_category,
                'status'=>$this->status,
                'time_limit'=>$this->task_two->time_limit ?? 40,
                'question'=>new WritingTaskTwoResource($this->task_two ) ?? [],
            ];
        }elseif ($this->exercise->part_type=='3'){
            return [
                'id'=>$this->id,
                'name'=>$this->name,
                'part_category'=>$part_category,
                'part_category_number'=>$this->part_category,
                'status'=>$this->status,
                'time_limit'=>$this->task_one->time_limit+$this->task_two->time_limit ?? 60,
                'task_one'=>new WritingTaskOneResource($this->task_one ) ?? [],
                'task_two'=>new WritingTaskTwoResource($this->task_two ) ?? [],
            ];
        }elseif ($this->exercise->part_type=='4'){
            return [
                'id'=>$this->id,
                'name'=>$this->name,
                'part_category'=>$part_category,
                'part_category_number'=>$this->part_category,
                'status'=>$this->status,
                'time_limit'=>$this->task_one->time_limit+$this->task_two->time_limit ?? 60,
                'task_one'=>new WritingTaskOneResource($this->task_one ) ?? [],
                'task_two'=>new WritingTaskTwoResource($this->task_two ) ?? [],
            ];
        }elseif ($this->exercise->part_type==null){
            return [
                'id'=>$this->id,
                'name'=>$this->name,
                'part_category'=>$part_category,
                'part_category_number'=>$this->part_category,
                'status'=>$this->status,
                'time_limit'=>$this->time_limit ?? 60,
                'task_one'=>new WritingTaskTwoResource($this->task_two ) ?? [],
                'task_two'=>new WritingTaskTwoResource($this->task_two ) ?? [],
            ];
        }else{
            return [];
        }
    }
}
