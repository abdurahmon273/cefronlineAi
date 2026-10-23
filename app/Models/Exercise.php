<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exercise extends Model
{
    use HasFactory,softDeletes;

    protected $fillable=[
        'name',
        'category_type',
        'part_type',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const  CATEGORY_TYPES = [
        '0'=>'Listening',
        '1'=>'Reading',
        '2'=>'Writing',
        '3'=>'Speaking',
        '4'=>'Full Mock',
    ];
   public const  PART_TYPES = [
        '1'=>'Task One',
        '2'=>'Task Two',
        '3'=>'Full Mock',
    ];
   public const  SPEAKING_PART_TYPES = [
        '1'=>'PART One',
        '2'=>'PART Two',
        '3'=>'PART THREE',
        '4'=>'Full Mock',
    ];
    public const  CATEGORY_LINKS = [
        '0'=>'Listening',
        '1'=>'Reading',
        '2'=>'Writing',
        '3'=>'Speaking',
        '4'=>'Full Mock',
    ];

    public function listening_exercises()
    {
        return $this->hasMany(ListeningExercise::class,'exercise_id');
    }
    public function reading_exercises()
    {
        return $this->hasMany(ReadingExercise::class,'exercise_id');
    }
    public function writing_exercises()
    {
        return $this->hasMany(WritingExercise::class,'exercise_id');
    }
    public function speaking_exercises()
    {
        return $this->hasMany(SpeakingExercise::class,'exercise_id');
    }
    public function full_exercises()
    {
        return $this->hasMany(FullExercise::class,'exercise_id');
    }

    public function listening_exercise()
    {
        return $this->hasOne(ListeningExercise::class,'exercise_id');
    }
    public function reading_exercise()
    {
        return $this->hasOne(ReadingExercise::class,'exercise_id');
    }
    public function writing_exercise()
    {
        return $this->hasOne(WritingExercise::class,'exercise_id');
    }
    public function speaking_exercise()
    {
        return $this->hasOne(SpeakingExercise::class,'exercise_id');
    }
    public function full_exercise()
    {
        return $this->hasOne(FullExercise::class,'exercise_id');
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
