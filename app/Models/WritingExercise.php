<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WritingExercise extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable=[
        'name',
        'part_category',
        'status',
        'exercise_id'
    ];

    public const PART_CATEGORIES=[
        '1'=>'task 1',
        '2'=>'task 2',
        '3'=>'Full mock',
    ];

    public function task_one()
    {
        return $this->hasOne(WritingTaskOne::class,'writing_exercise_id');
    }
    public function task_two()
    {
        return $this->hasOne(WritingTaskTwo::class,'writing_exercise_id');
    }
    public function full_exercise()
    {
        return $this->hasOne(FullExercise::class,'writing_exercise_id');
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
