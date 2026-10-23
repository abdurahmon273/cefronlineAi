<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpeakingPartOneSecondResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'speaking_result_id',
        'score',
        'result_json',
    ];



    public function answers()
    {
       return $this->hasMany(SpeakingPartOneSecondAnswer::class, 'one_second_result_id', 'id');
    }
}
