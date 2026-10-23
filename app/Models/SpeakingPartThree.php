<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpeakingPartThree extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'time',
        'theme',
        'modal_answer',
        'question_text',
        'theme_audio_url',
        'audio_url',
        'speaking_exercise_id',
    ];

    public function questions()
    {
        return $this->hasMany(SpeakingPartThreeQuestions::class,'part_three_id');
    }

    public function speaking_exercise()
    {
        return $this->belongsTo(SpeakingExercise::class);
    }
}
