<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ListeningPartThreeFour extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable=[
        'audio',
        'advice',
        'type',
        'text',
        'image',
        'listening_exercise_id',
    ];

    protected $TYPES=[
        '0'=>'part3',
        '1'=>'part4'
    ];

    public function part_three_four_questions()
    {
        return $this->hasMany(ListeningPartThreeFourQuestion::class,'listening_part_three_four_id');
    }
    public function part_three_four_answers()
    {
        return $this->hasMany(ListeningPartThreeFourAnswers::class ,'listening_part_three_four_id');
    }
    public function questions()
    {
        return $this->hasMany(ListeningPartThreeFourQuestion::class,'listening_part_three_four_id');
    }
    public function answers()
    {
        return $this->hasMany(ListeningPartThreeFourAnswers::class ,'listening_part_three_four_id');
    }

    public function listening_exercise()
    {
        return $this->belongsTo(ListeningExercise::class,'listening_exercise_id');
    }

}
