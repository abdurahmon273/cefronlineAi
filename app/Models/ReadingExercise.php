<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReadingExercise extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable=[
        'name',
        'part_category',
        'status',
        'difficulty',
        'time_limit',
        'exercise_id',
        'created_at',
        'deleted_at',
    ];


    public  const  DIFFICULTIES=[
        '0'=>'easy',
        '1'=>'medium',
        '2'=>'difficult',
    ];

    public  const  PART_CATEGORIES=[
        '1'=>'part 1',
        '2'=>'part 2',
        '3'=>'part 3',
        '4'=>'part 4',
        '5'=>'part 5',
        '6'=>'full',
    ];

    public const STATUS = [
        '1' => 'premium',
        '0' => 'free',
    ];

    public const PART_LINKS = [
        '1'=>'admin.reading.part1.index',
        '2'=>'admin.reading.part2.index',
        '3'=>'admin.reading.part3.index',
        '4'=>'admin.reading.part4.index',
        '5'=>'admin.reading.part5.index',
        '6'=>'admin.reading.full_exercise.index',
    ];

    public function exercise()
    {
        return $this->belongsTo(Exercise::class,'exercise_id')->withTrashed();
    }

    public function part_one()
    {
        return  $this->hasOne(ReadingPartOneFiveQuestion::class,'reading_exercise_id')->where('type','0');
    }
    public function part_two()
    {
        return  $this->hasOne(ReadingPartTwoThree::class,'reading_exercise_id')->where('type','0');
    }
    public function part_three()
    {
        return  $this->hasOne(ReadingPartTwoThree::class,'reading_exercise_id')->where('type','1');
    }
    public function part_four()
    {
        return  $this->hasOne(ReadingPartFour::class,'reading_exercise_id');
    }
    public function part_five()
    {
        return  $this->hasOne(ReadingPartOneFiveQuestion::class,'reading_exercise_id')->where('type','1');
    }

    public function full_exercise()
    {
        return $this->hasOne(FullExercise::class,'reading_exercise_id');
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
