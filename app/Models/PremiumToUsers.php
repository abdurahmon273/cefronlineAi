<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PremiumToUsers extends Model
{
    use HasFactory,softDeletes;

    protected $table = 'premium_to_users';

    protected $fillable = [
        'user_id',
        'start_time',
        'end_time',
        'type',
        'price',
        'admin_id',
        'plan_id',
        'promocode_id',
        'discount',
    ];

    public const TYPES=[
        '0'=>'app',
        '1'=>'by admin',
        '2'=>'web',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
    public function admin()
    {
        return $this->belongsTo(User::class,'admin_id')->withTrashed();
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class)->withTrashed();
    }

    public function promoCode()
    {
        return $this->belongsTo(Promocode::class,'promocode_id')->withTrashed();
    }
}
