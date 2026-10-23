<?php

namespace App\Http\Resources\Reading;

use App\Http\Resources\Listenings\ListeningPartOneTestResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReadingPartOneResource extends JsonResource
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
            'advice'=>$this->advice,
            'title'=>$this->title ?? null,
            'text'=>$this->question_text,
            'answers'=> ReadingPartOneAnswersResource::collection($this->answers),
        ];
    }
}
