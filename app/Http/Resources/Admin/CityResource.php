<?php

namespace App\Http\Resources\Admin;

use App\Models\Region;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'region_id'=>$this->region
        ];
    }
}
