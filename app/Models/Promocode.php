<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promocode extends Model
{
    use HasFactory,softDeletes;

    public const TYPES=[
        '0'=>'once use',
        '1'=>'many use',
    ];

    public const STATUS=[
        '0'=>'Generated',
        '1'=>'By Admin',
    ];
    protected $fillable = [
        'code',
        'type',
        'start_date',
        'end_date',
        'discount',
        'status',

    ];
    private const ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789';

    public static function random(int $length = 10): string
    {
        $alphabet = self::ALPHABET;
        $max = strlen($alphabet) - 1;
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= $alphabet[random_int(0, $max)];
        }
        return $str;
    }
    public function plans()
    {
        return $this->belongsToMany(Plan::class,'plan_promocode','promocode_id','plan_id');
    }

    public function packages()
    {
        return $this->belongsToMany(Package::class,'package_promocode','promocode_id','package_id');
    }

    public function premium_to_users()
    {
        return $this->hasMany(PremiumToUsers::class ,'promocode_id','id');
    }
    public function package_to_users()
    {
        return $this->hasMany(BalanceEdit::class ,'promocode_id','id');
    }
}
