<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasFactory, softDeletes;

    protected $fillable = [
        'price',
        'credit_price',
        'writing_count',
        'speaking_count',
        'order_id',
    ];


    public function balance_edits()
    {
        return $this->hasMany(BalanceEdit::class , 'package_id', 'id');
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
