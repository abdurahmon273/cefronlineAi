<?php

namespace App\Http\Resources\Reading;

use App\Models\ListeningPartThreeFourAnswers;
use App\Models\ReadingPartTwoThreeAnswer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReadingPartTwoResource extends JsonResource
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
            'letter_array'=>ReadingPartTwoThreeAnswer::LETTERS,
            'texts'=>ReadingPartTwotextsResource::collection($this->texts),
            'answers'=>ReadingPartTwoAnswersResource::collection($this->answers),
        ];
    }
}
