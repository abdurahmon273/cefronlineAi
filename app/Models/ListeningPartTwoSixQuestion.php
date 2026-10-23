<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ListeningPartTwoSixQuestion extends Model
{
    use HasFactory,softDeletes;

    protected $fillable = [
        'audio',
        'advice',
        'text',
        'title',
        'type',
        'listening_exercise_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $TYPES=[
        '0'=>'part2',
        '1'=>'part6'
    ];
    public function listening_part_two_six_tests()
    {
        return $this->hasMany(ListeningPartTwoSixTest::class,'listening_part_two_six_question_id');
    }

    public function answers()
    {
        return $this->hasMany(ListeningPartTwoSixTest::class,'listening_part_two_six_question_id');
    }
    public function listeningExercise()
    {
        return $this->belongsTo(ListeningExercise::class,'listening_exercise_id');
    }
}
