<?php

namespace App\Http\Resources\Speaking;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpeakingPartOneQuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'modal_answer'=>$this->modal_answer,
            'question'=>$this->question,
            'time'=>$this->time
        ];
    }
}
