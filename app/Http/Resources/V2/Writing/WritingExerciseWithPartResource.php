<?php

namespace App\Http\Resources\V2\Writing;

use App\Http\Resources\Reading\ReadingPartFiveResource;
use App\Http\Resources\Reading\ReadingPartFourResource;
use App\Http\Resources\Reading\ReadingPartOneResource;
use App\Http\Resources\Reading\ReadingPartTwoResource;
use App\Http\Resources\V2\Writing\WritingTaskOneResource;
use App\Http\Resources\V2\Writing\WritingTaskTwoResource;
use App\Models\ReadingExercise;
use App\Models\WritingExercise;
use App\Traits\PrepareTextTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WritingExerciseWithPartResource extends JsonResource
{
    use PrepareTextTrait;

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
                'time_limit'=> 30,
                'prepare_text'=>$this->GetPrepareTextByType(1),
                'question'=>new WritingTaskOneResource($this->task_one ) ?? [],
            ];
        }elseif ($this->exercise->part_type=='2'){
            return [
                'id'=>$this->id,
                'name'=>$this->name,
                'part_category'=>$part_category,
                'part_category_number'=>$this->part_category,
                'status'=>$this->status,
                'time_limit'=> 30,
                'prepare_text'=>$this->GetPrepareTextByType(2),
                'question'=>new WritingTaskTwoResource($this->task_two ) ?? [],
            ];
        }elseif ($this->exercise->part_type=='3'){
            return [
                'id'=>$this->id,
                'name'=>$this->name,
                'part_category'=>$part_category,
                'part_category_number'=>$this->part_category,
                'status'=>$this->status,
                'time_limit'=> 60,
                'prepare_text'=>($this->GetPrepareTextByType(1)).($this->GetPrepareTextByType(2)),
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
                'time_limit'=> 60,
                'prepare_text'=>($this->GetPrepareTextByType(1)).($this->GetPrepareTextByType(2)),
                'task_one'=>new WritingTaskOneResource($this->task_one ) ?? [],
                'task_two'=>new WritingTaskTwoResource($this->task_two ) ?? [],
            ];
        }else{
            return [];
        }
    }
}
