<?php

namespace App\Http\Resources\Listenings;

use App\Models\ListeningExercise;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListeningExerciseWithPartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $part_category='';
        if (isset(ListeningExercise::PART_CATEGORIES[$this->part_category])){
            $part_category=ListeningExercise::PART_CATEGORIES[$this->part_category];
        }
        if ($this->part_category=='1'){
            return [
                'id'=>$this->id,
                'name'=>$this->name,
                'part_category'=>$part_category,
                'part_category_number'=>$this->part_category,
                'status'=>$this->status,
                'difficulty'=>$this->difficulty,
                'time_limit'=>$this->time_limit ?? 6,
                'question'=>new ListeningPartOneResource($this->listening_part_one_fife_question ) ?? [],
            ];
        }elseif ($this->part_category=='2'){
            return [
                'id'=>$this->id,
                'name'=>$this->name,
                'part_category'=>$part_category,
                'part_category_number'=>$this->part_category,
                'status'=>$this->status,
                'difficulty'=>$this->difficulty,
                'time_limit'=>$this->time_limit ?? 6,
                'question'=>new ListeningPartTwoResource($this->listening_part_two_six_question ) ?? [],
            ];
        }elseif ($this->part_category=='3'){
            return [
                'id'=>$this->id,
                'name'=>$this->name,
                'part_category'=>$part_category,
                'part_category_number'=>$this->part_category,
                'status'=>$this->status,
                'difficulty'=>$this->difficulty,
                'time_limit'=>$this->time_limit ?? 6,
                'question'=>new ListeningPartThreeResource($this->listening_part_three_four ) ?? [],
            ];
        }elseif ($this->part_category=='4'){
            return [
                'id'=>$this->id,
                'name'=>$this->name,
                'part_category'=>$part_category,
                'part_category_number'=>$this->part_category,
                'status'=>$this->status,
                'difficulty'=>$this->difficulty,
                'time_limit'=>$this->time_limit ?? 6,
                'question'=>new ListeningPartFourResource($this->listening_part_three_four ) ?? [],
            ];
        }elseif ($this->part_category=='5'){
            return [
                'id'=>$this->id,
                'name'=>$this->name,
                'part_category'=>$part_category,
                'part_category_number'=>$this->part_category,
                'status'=>$this->status,
                'difficulty'=>$this->difficulty,
                'time_limit'=>$this->time_limit ?? 6,
                'question'=>new ListeningPartFiveResource($this->listening_part_one_fife_question ) ?? [],
            ];
        }elseif ($this->part_category=='6'){
            return [
                'id'=>$this->id,
                'name'=>$this->name,
                'part_category'=>$part_category,
                'part_category_number'=>$this->part_category,
                'status'=>$this->status,
                'difficulty'=>$this->difficulty,
                'time_limit'=>$this->time_limit ?? 6,
                'question'=>new ListeningPartSixResource($this->listening_part_two_six_question ) ?? [],
            ];
        }elseif ($this->part_category=='7'){
            $array = [1 => 23, 2 => 26, 3 => 28, 4 => 30, 5 => 33, 6 => 34, 7 => 36, 8 => 38, 9 => 39, 10 => 41, 11 => 42, 12 => 44, 13 => 45, 14 => 47, 15 => 48, 16 => 50, 17 => 51, 18 => 53, 19 => 54, 20 => 55, 21 => 57, 22 => 58, 23 => 60, 24 => 61, 25 => 63, 26 => 65, 27 => 66, 28 => 68, 29 => 70, 30 => 72, 31 => 73, 32 => 74, 33 => 75, 34 => 75, 35 => 75,];


            return [
                'id'=>$this->id,
                'name'=>$this->name,
                'part_category'=>$part_category,
                'part_category_number'=>$this->part_category,
                'status'=>$this->status,
                'difficulty'=>$this->difficulty,
                'time_limit'=>$this->time_limit ?? 6,
                'result_array'=>$array,
                'part_one'=>new ListeningPartOneResource($this->part_one ) ?? [],
                'part_two'=>new ListeningPartTwoResource($this->part_two ) ?? [],
                'part_three'=>new ListeningPartThreeResource($this->part_three ) ?? [],
                'part_four'=>new ListeningPartFourResource($this->part_four ) ?? [],
                'part_five'=>new ListeningPartFiveResource($this->part_five ) ?? [],
                'part_six'=>new ListeningPartSixResource($this->part_six ) ?? [],
            ];
        }else{
            return [];
        }


    }
}
