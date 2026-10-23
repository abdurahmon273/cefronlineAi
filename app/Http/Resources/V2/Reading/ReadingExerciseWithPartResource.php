<?php

namespace App\Http\Resources\V2\Reading;

use App\Http\Resources\Listenings\ListeningPartOneResource;
use App\Http\Resources\Reading\ReadingPartFiveResource;
use App\Http\Resources\Reading\ReadingPartFourResource;
use App\Http\Resources\Reading\ReadingPartOneResource;
use App\Http\Resources\Reading\ReadingPartTwoResource;
use App\Models\ListeningExercise;
use App\Models\ReadingExercise;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReadingExerciseWithPartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $part_category='';
        if (isset(ReadingExercise::PART_CATEGORIES[$this->part_category])){
            $part_category=ReadingExercise::PART_CATEGORIES[$this->part_category];
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
                'part_one'=>new ReadingPartOneResource($this->part_one ) ?? [],
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
                'part_two'=>new ReadingPartTwoResource($this->part_two ) ?? [],
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
                'part_three'=>new ReadingPartTwoResource($this->part_three ) ?? [],
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
                'part_four'=>new ReadingPartFourResource($this->part_four ) ?? [],
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
                'part_five'=>new ReadingPartFiveResource($this->part_five ) ?? [],
            ];
        }elseif ($this->part_category=='6'){
            $array = [1 => 20, 2 => 24, 3 => 27, 4 => 29, 5 => 32, 6 => 34, 7 => 36, 8 => 38, 9 => 39, 10 => 41, 11 => 42, 12 => 44, 13 => 45, 14 => 46, 15 => 48, 16 => 49, 17 => 51, 18 => 52, 19 => 54, 20 => 55, 21 => 57, 22 => 58, 23 => 60, 24 => 61, 25 => 63, 26 => 65, 27 => 66, 28 => 68, 29 => 70, 30 => 71, 31 => 73, 32 => 74, 33 => 75, 34 => 75, 35 => 75,];

            return [
                'id'=>$this->id,
                'name'=>$this->name,
                'status'=>$this->status,
                'part_category'=>$part_category,
                'part_category_number'=>$this->part_category,
                'difficulty'=>$this->difficulty,
                'time_limit'=>$this->time_limit ?? 6,
                'result_array'=>$array,
                'part_one'=>new ReadingPartOneResource($this->part_one ) ?? [],
                'part_two'=>new ReadingPartTwoResource($this->part_two ) ?? [],
                'part_three'=>new ReadingPartTwoResource($this->part_three ) ?? [],
                'part_four'=>new ReadingPartFourResource($this->part_four ) ?? [],
                'part_five'=>new ReadingPartFiveResource($this->part_five ) ?? [],
            ];
        }else{
            return [];
        }
    }
}
