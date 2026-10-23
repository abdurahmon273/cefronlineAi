<?php

namespace App\Http\Resources\Reading;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReadingPartFourResource extends JsonResource
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
            'advice'=>$this->advice,
            'text'=>$this->text,
            'TFNg_tests'=>ReadingPartFourTFNgResource::collection($this->tfn_tests),
            'MCHQ_tests'=>ReadingPartFourMCHQResource::collection($this->mchq_tests),
        ];
    }
}
