<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpeakingPartOneFirstResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'speaking_result_id',
        'score',
        'result_json',
    ];

    public function answers()
    {
       return $this->hasMany(SpeakingPartOneFirstAnswer::class, 'one_first_result_id', 'id');
    }
}
