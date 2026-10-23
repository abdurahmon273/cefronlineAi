<?php

namespace App\Http\Resources\Listenings;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListeningPartThreeAnswerResource extends JsonResource
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
            'letter_key'=>$this->letter,
            'answer'=>$this->answer
        ];
    }
}
