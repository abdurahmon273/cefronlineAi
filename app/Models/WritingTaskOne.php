<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WritingTaskOne extends Model
{
    use HasFactory;

    protected $fillable=[
        'theme',
        'requirement',
        'advice',
        'time_limit',
        'word_count',
        'example_text',
        'bullet_point_1',
        'bullet_point_2',
        'bullet_point_3',
        'writing_exercise_id',
    ];
}
