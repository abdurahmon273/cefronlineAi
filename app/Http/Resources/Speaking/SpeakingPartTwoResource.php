<?php

namespace App\Http\Resources\Speaking;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpeakingPartTwoResource extends JsonResource
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
            'modal_answer'=>$this->modal_answer,
            'title'=>$this->title,
            'images'=> SpeakingPartOneSecondImageResource::collection($this->images),
            'questions'=> SpeakingPartTwoQuestionResource::collection($this->questions) ?? [] ,
        ];
    }
}
