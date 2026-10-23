<?php

namespace App\Http\Resources\Writing;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WritingTaskTwoResource extends JsonResource
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
        ];
    }
}
