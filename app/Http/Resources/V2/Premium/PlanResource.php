<?php

namespace App\Http\Resources\V2\Premium;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
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

        if ($this->premium_to_users
                ->where('start_time', '<=', Carbon::now()->format('Y-m-d\TH:i'))  // Ensure start_time is before or equal to now
                ->where('end_time', '>=', Carbon::now()->format('Y-m-d\TH:i'))  // Ensure end_time is after or equal to now
                ->count() > 0){

            $boolean=true;
        }else{
            $boolean=false;
        }

        }


        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'days'=>$this->day,
            'price'=>$this->price,
            'credit_price'=>$this->credit_price,
            'writing_count'=>$this->writing_count ?? 0,
            'speaking_count'=>$this->speaking_count ?? 0,
            'status'=>$boolean,
            'discount'=>$this->discount ?? null,
            'discounted_price'=>$this->discounted_price ?? $this->price,
        ];
    }
}
