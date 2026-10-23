<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WritingTaskTwo extends Model
{
    use HasFactory;
    protected $fillable=[
        'theme',
        'requirement',
        'advice',
        'writing_exercise_id',
        'time_limit',
        'word_count',
        'example_text',
    ];
}
