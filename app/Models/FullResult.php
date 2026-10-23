<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FullResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'status',
        'user_id',
        'full_exercise_id',
        'listening_result_id',
        'reading_result_id',
        'writing_result_id',
        'speaking_result_id',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function exercise()
    {
        return $this->belongsTo(FullExercise::class, 'full_exercise_id');
    }

    public function listening_result()
    {
        return $this->belongsTo(ListeningResult::class, 'listening_result_id');
    }

    public function reading_result(){
        return $this->belongsTo(ReadingResult::class, 'reading_result_id');
    }

    public function writing_result(){
        return $this->belongsTo(WritingResult::class, 'writing_result_id');
    }
    public function speaking_result(){
        return $this->belongsTo(SpeakingResult::class, 'speaking_result_id')->withTrashed();
    }

}
