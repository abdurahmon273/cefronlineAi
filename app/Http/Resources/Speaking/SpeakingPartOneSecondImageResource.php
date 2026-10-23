<?php

namespace App\Http\Resources\Speaking;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpeakingPartOneSecondImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'image'=>$this->image ? url('storage/speaking_images/'.$this->image) : null,
        ];
    }
}
