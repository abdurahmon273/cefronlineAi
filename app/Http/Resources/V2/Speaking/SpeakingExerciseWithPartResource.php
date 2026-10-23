<?php

namespace App\Http\Resources\V2\Speaking;

use App\Models\SpeakingExercise;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpeakingExerciseWithPartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $part_category='';
        if (isset(SpeakingExercise::PART_CATEGORIES[$this->part_category])){
            $part_category=SpeakingExercise::PART_CATEGORIES[$this->part_category];
        }

        if ($this->part_category=='1'){
            return [
                'id'=>$this->id,
                'title'=>$this->title,
                'part_category'=>$part_category,
                'part_category_number'=>$this->part_category,
                'status'=>$this->status,
                'time_limit'=>round(($this->part_one_first->questions->sum('time') + $this->part_one_second->questions->sum('time')) / 60) ?? 3,
                'part_one_first'=>new SpeakingPartOneResource( $this->part_one_first ) ?? [],
                'part_one_second'=>new SpeakingPartOneSecondResource( $this->part_one_second ) ?? [],
            ];
        }elseif ($this->exercise->part_type=='2'){

            return [
                'id'=>$this->id,
                'title'=>$this->title,
                'part_category'=>$part_category,
                'part_category_number'=>$this->part_category,
                'status'=>$this->status,
                'time_limit'=>($this->part_two->time/60) ?? 3,
                'start_time'=>$this->part_two->start_time ?? 60,
                'part_two'=>new SpeakingPartTwoResource($this->part_two ) ?? [],
            ];
        }elseif ($this->exercise->part_type=='3'){
            return [
                'id'=>$this->id,
                'title'=>$this->title,
                'part_category'=>$part_category,
                'part_category_number'=>$this->part_category,
                'status'=>$this->status,
                'time_limit'=>$this->time_limit ?? 3,
                'start_time'=>$this->part_three->start_time ?? 60,
                'part_three'=>new SpeakingPartThreeResource($this->part_three ) ?? [],
            ];
        }elseif ($this->exercise->part_type=='4'){
            return [
                'id'=>$this->id,
                'title'=>$this->title,
                'status'=>$this->status,
                'part_category'=>$part_category,
                'part_category_number'=>$this->part_category,
                'time_limit'=>$this->time_limit ?? 9,
                'part_two_start_time'=>$this->part_two->start_time ?? 60,
                'part_three_start_time'=>$this->part_three->start_time ?? 60,
                'part_one_first'=>new SpeakingPartOneResource( $this->part_one_first ) ?? [],
                'part_one_second'=>new SpeakingPartOneSecondResource( $this->part_one_second ) ?? [],
                'part_two'=>new SpeakingPartTwoResource($this->part_two ) ?? [],
                'part_three'=>new SpeakingPartThreeResource($this->part_three ) ?? [],
            ];
        }elseif ($this->exercise->part_type==null){
            return [
                'id'=>$this->id,
                'title'=>$this->title,
                'status'=>$this->status,
                'part_category'=>$part_category,
                'part_category_number'=>$this->part_category,
                'time_limit'=>$this->time_limit ?? 9,
                'part_two_start_time'=>$this->part_two->start_time ?? 60,
                'part_three_start_time'=>$this->part_three->start_time ?? 60,
                'example_answer'=>$this->example_answer,
                'part_one_first'=>new SpeakingPartOneResource( $this->part_one_first ) ?? [],
                'part_one_second'=>new SpeakingPartOneSecondResource( $this->part_one_second ) ?? [],
                'part_two'=>new SpeakingPartTwoResource($this->part_two ) ?? [],
                'part_three'=>new SpeakingPartThreeResource($this->part_three ) ?? [],
            ];
        }else{
            return [];
        }
    }
}
