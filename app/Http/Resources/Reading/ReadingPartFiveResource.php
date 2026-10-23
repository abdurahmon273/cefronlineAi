<?php

namespace App\Http\Resources\Reading;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReadingPartFiveResource extends JsonResource
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
            'reading_text'=>$this->reading_text,
            'question_text'=>$this->question_text,
            'answers'=>ReadingPartFiveAnswerResource::collection($this->answers),
            'tests'=>ReadingPartFiveTestResource::collection($this->tests)
        ];
    }
}
