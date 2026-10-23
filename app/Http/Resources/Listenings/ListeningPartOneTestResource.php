<?php

namespace App\Http\Resources\Listenings;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListeningPartOneTestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            "answer_1"=>$this->answer_1,
            "answer_2"=>$this->answer_2,
            "answer_3"=>$this->answer_3,
            "correct_answer"=>$this->correct_answer,
        ];
    }
}
