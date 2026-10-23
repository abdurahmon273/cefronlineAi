<?php

namespace App\Http\Resources\Full;

use App\Http\Resources\Listenings\ListeningExerciseWithPartResource;
use App\Http\Resources\Reading\ReadingExerciseWithPartResource;
use App\Http\Resources\Speaking\SpeakingExerciseWithPartResource;
use App\Http\Resources\Speaking\SpeakingPartOneResource;
use App\Http\Resources\Speaking\SpeakingPartOneSecondResource;
use App\Http\Resources\Writing\WritingExerciseWithPartResource;
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
        $listening_exercise=$this->listening_exercise->part_one ? (count($this->listening_exercise->part_one->tests)>=1 ? new ListeningExerciseWithPartResource($this->listening_exercise) : null) : null;
        $reading_exercise=$this->reading_exercise->part_one? (count($this->reading_exercise->part_one->answers)>=1 ? new ReadingExerciseWithPartResource($this->reading_exercise) :null) : null;
        $writing_exercise=$this->writing_exercise->task_one ? new WritingExerciseWithPartResource($this->writing_exercise) : null;
        $speaking_exercise=$this->speaking_exercise->part_one_first ? ( count($this->speaking_exercise->part_one_first->questions)>=1 ? new SpeakingExerciseWithPartResource($this->speaking_exercise) : null ) : null;
        return [
            'id'=>$this->id,
            'title'=>$this->title,
            'status'=>$this->status,
            'time_limit'=>
                ($this->listening_exercise->time_limit ?? 35)+
                ($this->reading_exercise->time_limit ?? 70)+
                (($this->writing_exercise->task_one ? $this->writing_exercise->task_one->time_limit  : 20 )+
                    ($this->writing_exercise->task_two? $this->writing_exercise->task_two->time_limit : 40))+
                ((($this->speaking_exercise->part_one_first ? $this->speaking_exercise->part_one_first->questions->sum('time') : 120 )/60)+
                    (($this->speaking_exercise->part_one_second? $this->speaking_exercise->part_one_second->questions->sum('time') : 120)/60)+
                    (($this->speaking_exercise->part_two? $this->speaking_exercise->part_two->time : 120)/60)+
                    (($this->speaking_exercise->part_three ? $this->speaking_exercise->part_three->time : 120))/60) ?? null,
            'listening_exercise'=>$listening_exercise,
            'reading_exercise'=>$reading_exercise,
            'writing_exercise'=>$writing_exercise,
            'speaking_exercise'=>$speaking_exercise,
        ];
    }
}
