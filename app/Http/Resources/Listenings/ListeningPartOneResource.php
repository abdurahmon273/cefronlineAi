<?php

namespace App\Http\Resources\Listenings;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListeningPartOneResource extends JsonResource
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
            'tests'=> ListeningPartOneTestResource::collection($this->tests),
        ];
    }
}
