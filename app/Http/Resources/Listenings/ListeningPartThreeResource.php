<?php

namespace App\Http\Resources\Listenings;

use App\Models\ListeningPartThreeFourAnswers;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListeningPartThreeResource extends JsonResource
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
            'audio'=>$this->audio ? url('storage/audios/'.$this->audio) : null,
            'advice'=>$this->advice,
            'letter_array'=>ListeningPartThreeFourAnswers::LETTERS,
            'speakers'=> ListeningPartThreeSpeakerResource::collection($this->part_three_four_questions),
            'answers'=>ListeningPartThreeAnswerResource::collection($this->part_three_four_answers),
        ];
    }
}
