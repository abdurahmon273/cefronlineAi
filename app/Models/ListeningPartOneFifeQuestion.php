<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ListeningPartOneFifeQuestion extends Model
{
    use HasFactory,softDeletes;

    protected $fillable = [
        'audio',
        'advice',
        'type',
        'listening_exercise_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $TYPES=[
        '0'=>'part1',
        '1'=>'part5'
    ];

    public function tests()
    {
        return $this->hasMany(ListeningPartOneFifeTest::class,'listening_part_one_fife_question_id');
    }

    public function listeningExercise()
    {
       return $this->belongsTo(ListeningExercise::class,'listening_exercise_id');
    }

}
