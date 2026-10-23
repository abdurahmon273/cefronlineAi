<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BalanceEdit extends Model
{
    use HasFactory, softDeletes;

    protected $table = 'balance_edits';

    protected $fillable = [
        'user_id',
        'price',
        'value',
        'current_currency',
        'current_tax',
        'discount',
        'current_balance',
        'type',
        'admin_id',
        'package_id',
        'promocode_id',
    ];

    public const TYPES=[
        0=>'user',
        1=>'by admin',
        2=>'by plan'
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
    public function admin()
    {
        return $this->belongsTo(User::class,'admin_id')->withTrashed();
    }
    public function package()
    {
        return $this->belongsTo(Package::class,'package_id')->withTrashed();
    }

    public function promoCode()
    {
        return $this->belongsTo(Promocode::class,'promocode_id')->withTrashed();
    }

}
