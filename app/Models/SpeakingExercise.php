<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpeakingExercise extends Model
{
    use HasFactory,softDeletes;

    protected $fillable=[
        'title',
        'part_category',
        'status',
        'example_answer',
        'exercise_id',
    ];

    public const PART_CATEGORIES=[
        '1'=>'Part 1',
        '2'=>'Part 2',
        '3'=>'Part 3',
        '4'=>'Full Mock',
    ];
    public function part_one_first()
    {
        return $this->hasOne(SpeakingPartOneFirst::class,'speaking_exercise_id');
    }
    public function part_one_second()
    {
        return $this->hasOne(SpeakingPartOneSecond::class,'speaking_exercise_id');
    }
    public function part_two()
    {
        return $this->hasOne(SpeakingPartTwo::class,'speaking_exercise_id');
    }
    public function part_three()
    {
        return $this->hasOne(SpeakingPartThree::class,'speaking_exercise_id');
    }
    public function full_exercise()
    {
        return $this->hasOne(FullExercise::class,'speaking_exercise_id');
    }
    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }
    public static function updateOrder($data)
    {
        foreach ($data as $datum) {
            $id = $datum['value'];
            $displayIndex = $datum['order'];

            static::where('id', $id)->update(['order_id' => $displayIndex]);
        }
    }
}
