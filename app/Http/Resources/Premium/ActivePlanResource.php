<?php

namespace App\Http\Resources\Premium;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivePlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'plan_id' => $this->plan_id,
            'plan_name' => $this->plan?->name,
            'type' => isset($this->type) ? (int) $this->type : null,
            'type_label' => $this->type !== null ? ($this->TYPES[(string) $this->type] ?? null) : null,
            'price' => $this->price,
            'discount' => $this->discount,
            'promocode_id' => $this->promocode_id,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'is_active' => now()->between($this->start_time, $this->end_time),
        ];
    }
}
