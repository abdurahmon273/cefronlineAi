<?php

namespace App\Http\Resources\V2\Writing;

use App\Http\Resources\Reading\ReadingPartOneAnswersResource;
use App\Traits\WritingGetDataTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WritingTaskOneResource extends JsonResource
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
            'theme'=>$this->theme,
            'requirement'=>$this->requirement,
            'advice'=>$this->advice,
            'example_text' => $this->textToHtml($this->example_text),
            'bullet_point_1'=>$this->bullet_point_1,
            'bullet_point_2'=>$this->bullet_point_2,
            'bullet_point_3'=>$this->bullet_point_3,
        ];
    }
}
