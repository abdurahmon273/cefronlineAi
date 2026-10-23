<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Information extends Model
{
    use HasFactory,softDeletes;

    protected $fillable = [
      'information_link',
      'type',
      'order_id'
    ];

    const TYPES=[
        '0'=>'phone number',
        '1'=>'telegram',
        '2'=>'address',
    ];
}
