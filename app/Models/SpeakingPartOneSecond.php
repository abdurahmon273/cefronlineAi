<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpeakingPartOneSecond extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable=[
        'title',
        'speaking_exercise_id',
    ];
    public function questions()
    {
        return $this->hasMany(SpeakingPartOneSecondQuestions::class,'part_one_second_id');
    }
    public function images()
    {
        return $this->hasMany(SpeakingPartOneSecondImages::class,'part_one_second_id');
    }
}
