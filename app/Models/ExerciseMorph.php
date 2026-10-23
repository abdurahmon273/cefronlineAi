<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExerciseMorph extends Model
{
    use HasFactory;

    protected $fillable = ['exercise_id', 'subject_id', 'subject_type'];

    public function subject()
    {
        return $this->morphTo();
    }

    public function listening_exercise()
    {
        return $this->morphTo()
            ->where('subject_type', 'App\\Models\\ListeningSubject'); // Replace with your actual ListeningSubject model namespace
    }


    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }
}
