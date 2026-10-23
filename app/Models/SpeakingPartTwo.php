<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpeakingPartTwo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable=[
        'title',
        'time',
        'modal_answer',
        'speaking_exercise_id',
    ];

    public function questions()
    {
        return $this->hasMany(SpeakingPartTwoQuestions::class,'part_two_id');
    }
    public function question()
    {
        return $this->hasOne(SpeakingPartTwoQuestions::class,'part_two_id');
    }
    public function images()
    {
        return $this->hasMany(SpeakingPartTwoImage::class,'part_two_id');
    }

    public function speaking_exercise()
    {
        return $this->belongsTo(SpeakingExercise::class);
    }
}
