<?php

namespace App\Http\Resources\Listenings;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListeningPartTwoResource extends JsonResource
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
            'text'=>$this->text,
            'tests'=> ListeningPartTwoTestResource::collection($this->listening_part_two_six_tests),
        ];
    }
}
