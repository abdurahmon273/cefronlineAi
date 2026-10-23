<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpeakingPartOneFirst extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable=[
        'title',
        'speaking_exercise_id',
    ];

    public function questions()
    {
        return $this->hasMany(SpeakingPartOneFirstQuestions::class,'part_one_first_id');
    }
}
