<?php

namespace App\Http\Resources\Premium;

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
        $user=auth()->user();

        if ($user && auth()->user()->mobil_number=='+998773437715'){

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
            'status'=>$boolean,
            'user_id'=>$user->id,
            'discount'=>$this->discount ?? null,
            'discounted_price'=>$this->discounted_price ?? $this->price,
        ];
    }
}
