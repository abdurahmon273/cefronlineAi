<?php

namespace App\Http\Resources\Listenings;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListeningPartTwoTestResource extends JsonResource
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
            'count_number'=>$this->count_number,
            'correct_answer'=>$this->correct_answer,
            'extra_answer'=>$this->extra_answer,
        ];
    }
}
