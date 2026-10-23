<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReadingPartFour extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable=[
        'title',
        'text',
        'advice',
        'reading_exercise_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function tests()
    {
        return $this->hasMany(ReadingPartFourTest::class,'reading_part_four_id');
    }
    public function tfn_tests()
    {
        return $this->hasMany(ReadingPartFourTest::class,'reading_part_four_id')->where('type','1');
    }
    public function mchq_tests()
    {
        return $this->hasMany(ReadingPartFourTest::class,'reading_part_four_id')->where('type','2');
    }

    public function reading_exercise()
    {
       return $this->belongsTo(ReadingExercise::class,'reading_exercise_id');
    }
}
