<?php

namespace App\Http\Resources\V2\Premium;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreditResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $boolean=false;
        $user=auth('sanctum')->user();

        if ($user && $user->mobil_number=='+998773437715'){

            if ($this->balance_edits
                    ->where('value', '>', 0)
                    ->count() > 0
            ){

                $boolean=true;
            }else{
                $boolean=false;
            }

        }


        return [
            'id'=>$this->id,
            'price' => floatval($this->price),
            'writing_count'=>intval($this->writing_count),
            'speaking_count'=>intval($this->speaking_count),
            'status'=>$boolean,
            'discount'=>$this->discount ?? null,
            'discounted_price'=>$this->discounted_price ?? floatval($this->price),
        ];
    }
}
