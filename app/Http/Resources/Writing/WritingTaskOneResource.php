<?php

namespace App\Http\Resources\Writing;

use App\Http\Resources\Reading\ReadingPartOneAnswersResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WritingTaskOneResource extends JsonResource
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
            'theme'=>$this->theme,
            'requirement'=>$this->requirement,
            'advice'=>$this->advice,
            'example_text'=>$this->example_text,
            'bullet_point_1'=>$this->bullet_point_1,
            'bullet_point_2'=>$this->bullet_point_2,
            'bullet_point_3'=>$this->bullet_point_3,
        ];
    }
}
