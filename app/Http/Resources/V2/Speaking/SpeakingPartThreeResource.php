<?php

namespace App\Http\Resources\V2\Speaking;

use App\Traits\WritingGetDataTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpeakingPartThreeResource extends JsonResource
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
            'title'=>$this->title,
            'modal_answer'=>$this->textToHtml($this->modal_answer),
            'theme'=>$this->theme,
            'question_text'=>$this->question_text,
        ];
    }
}
