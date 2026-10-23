<?php

namespace App\Http\Resources\V2\Full;

use App\Http\Resources\V2\Listening\ListeningExerciseWithPartResource;
use App\Http\Resources\V2\Reading\ReadingExerciseWithPartResource;
use App\Http\Resources\V2\Speaking\SpeakingExerciseWithPartResource;
use App\Http\Resources\Speaking\SpeakingPartOneResource;
use App\Http\Resources\Speaking\SpeakingPartOneSecondResource;
use App\Http\Resources\V2\Writing\WritingExerciseWithPartResource;
use App\Services\FullMockService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FullExerciseWithPartsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $listening_exercise=$this->listening_exercise->part_one ?
            (count($this->listening_exercise->part_one->tests)>=1 ? new ListeningExerciseWithPartResource($this->listening_exercise) : null) : null;
        $reading_exercise=$this->reading_exercise->part_one?
            (count($this->reading_exercise->part_one->answers)>=1 ? new ReadingExerciseWithPartResource($this->reading_exercise) :null) : null;
        $writing_exercise=$this->writing_exercise->task_one ?
            new WritingExerciseWithPartResource($this->writing_exercise) : null;
        $speaking_exercise=$this->speaking_exercise->part_one_first ?
            ( count($this->speaking_exercise->part_one_first->questions)>=1 ?
                new SpeakingExerciseWithPartResource($this->speaking_exercise) : null ) : null;

        $resultData = app(FullMockService::class)->getFullResultData($this->id);

        return [
            'id'=>$this->id,
            'title'=>$this->title,
            'status'=>$this->status,
            'time_limit'=>($this->listening_exercise_id ?  35 : 0)+
                ($this->reading_exercise_id ?  70 : 0)+
                ($this->writing_exercise_id ?  60 : 0 )+
                ($this->speaking_exercise_id ?  10 : 0 ),
            'resultData'=>$resultData,
            'listening_exercise'=>$listening_exercise,
            'reading_exercise'=>$reading_exercise,
            'writing_exercise'=>$writing_exercise,
            'speaking_exercise'=>$speaking_exercise,
        ];
    }
}
