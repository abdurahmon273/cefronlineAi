<?php

namespace App\Http\Resources\Speaking;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpeakingPartOneSecondResource extends JsonResource
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
            'title'=>$this->title,
            'images'=> SpeakingPartOneSecondImageResource::collection($this->images),
            'questions'=> SpeakingPartOneSecondQuestionResource::collection($this->questions) ?? [] ,
        ];
    }
}
