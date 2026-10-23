<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FullExercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'status',
        'exercise_id',
        'listening_exercise_id',
        'reading_exercise_id',
        'writing_exercise_id',
        'speaking_exercise_id',
    ];

    public function listening_exercise()
    {
        return $this->belongsTo(ListeningExercise::class,'listening_exercise_id');
    }
    public function reading_exercise()
    {
        return $this->belongsTo(ReadingExercise::class,'reading_exercise_id');
    }
    public function writing_exercise()
    {
        return $this->belongsTo(WritingExercise::class,'writing_exercise_id');
    }
    public function speaking_exercise()
    {
        return $this->belongsTo(SpeakingExercise::class,'speaking_exercise_id');
    }
    public static function updateOrder($data)
    {
        foreach ($data as $datum) {
            $id = $datum['value'];
            $displayIndex = $datum['order'];

            static::where('id', $id)->update(['order_id' => $displayIndex]);
        }
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }
}
