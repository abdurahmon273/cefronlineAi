<?php

namespace App\Http\Resources\V2\Listening;

use App\Enums\CefrTypeEnum;
use App\Http\Resources\V2\Full\FullExerciseResource;
use App\Http\Resources\V2\Speaking\SpeakingExerciseResource;
use App\Http\Resources\V2\Reading\ReadingExercisesResource;
use App\Http\Resources\V2\Writing\WritingExerciseResource;
use App\Models\Exercise;
use App\Models\FullExercise;
use App\Models\ListeningExercise;
use App\Models\ReadingExercise;
use App\Models\SpeakingExercise;
use App\Traits\PrepareTextTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExerciseResource extends JsonResource
{
    use PrepareTextTrait;
    public function toArray(Request $request): array
    {


        if ($this->category_type==CefrTypeEnum::LISTENING->value){

            if (count($this->listening_exercises)>=0){

                return  [
                    'id'=>$this->id,
                    'title'=>$this->name,
                    'exercise_count'=>count($this->listening_exercises),
                    'listening_exercises'=>ListeningExerciseResource::collection($this->listening_exercises),
                ];
            }else{
                return  [
                    'id'=>$this->id,
                    'title'=>$this->name,
                    'exercise_count'=>0,
                    'listening_exercises'=>[],
                ];
            }

        }
        elseif ($this->category_type==CefrTypeEnum::READING->value){

            return  [
                'id'=>$this->id,
                'title'=>$this->name,
                'exercise_count'=>count($this->reading_exercises),
                'reading_exercises'=>ReadingExercisesResource::collection($this->reading_exercises),
            ];
        }
        elseif ($this->category_type==CefrTypeEnum::WRITING->value){
            $writing_exercise=$this->writing_exercises;

            return  [
                'id'=>$this->id,
                'title'=>$this->name,
                'part_type'=>Exercise::PART_TYPES[$this->part_type],
                'exercise_count'=>count($writing_exercise),
                'writing_exercises'=>WritingExerciseResource::collection($writing_exercise),
            ];
        }
        elseif ($this->category_type==CefrTypeEnum::SPEAKING->value){
            $speaking_exercise=$this->speaking_exercises;

                return  [
                    'id'=>$this->id,
                    'title'=>$this->name,
                    'exercise_count'=>count($speaking_exercise),
                    'speaking_exercises'=>SpeakingExerciseResource::collection($speaking_exercise),
                ];
            }
        elseif ($this->category_type==CefrTypeEnum::FULL_MOCK->value){


            return  [
                'id'=>$this->id,
                'title'=>$this->name,
                'exercise_count'=>count($this->full_exercises),
                'full_exercises'=>FullExerciseResource::collection($this->full_exercises),
            ];

        }else{
            return [];
        }

    }
}
