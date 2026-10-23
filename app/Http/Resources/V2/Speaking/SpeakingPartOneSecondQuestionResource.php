<?php

namespace App\Http\Resources\V2\Speaking;

use App\Traits\WritingGetDataTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpeakingPartOneSecondQuestionResource extends JsonResource
{
    use WritingGetDataTrait;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id'=>$this->id,
            'modal_answer'=>$this->textToHtml($this->modal_answer),
            'question'=>$this->question,
            'time'=>$this->time,
            'audio_link'=> $this->audio_url ? route('admin.speaking.audio.stream',$this->audio_url) : null,
        ];
    }
}
