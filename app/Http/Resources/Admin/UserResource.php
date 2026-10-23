<?php

namespace App\Http\Resources\Admin;

use App\Models\City;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'mobil_number'=>$this->mobil_number,
            'email'=>$this->email,
            'gender'=>(int)$this->gender,
            'birth_date'=>$this->birth_date,
            'surname'=>$this->surname,
            'image'=>$this->image,
            'city_id'=>$this->city_id ?? null,
        ];
    }
}
