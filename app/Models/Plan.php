<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'price',
        'day',
        'credit_price',
        'order_id',
    ];

    public function premium_to_users()
    {
        return $this->hasMany(PremiumToUsers::class , 'plan_id', 'id');
    }


    public static function updateOrder($data)
    {
        foreach ($data as $datum) {
            $id = $datum['value'];
            $displayIndex = $datum['order'];

            static::where('id', $id)->update(['order_id' => $displayIndex]);
        }
    }
}
