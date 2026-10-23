<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReadingPartOneFiveQuestion extends Model
{
    use HasFactory,softDeletes;

    protected $fillable = [
        'title',
        'reading_text',
        'advice',
        'question_text',
        'type',
        'reading_exercise_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $TYPES=[
        '0'=>'part 1',
        '1'=>'part 5'
    ];
    public function answers()
    {
        return $this->hasMany(ReadingPartOneFiveTest::class,'reading_part_one_five_question_id');
    }
    public function tests()
    {
        return $this->hasMany(ReadingPartFiveTest::class,'reading_part_one_five_question_id');
    }

    public function reading_exercise()
    {
        return $this->belongsTo(ReadingExercise::class,'reading_exercise_id')->withTrashed();
    }


    public function readingExercise()
    {
        return $this->belongsTo(ReadingExercise::class,'reading_exercise_id');
    }
}
