<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpeakingPartOneSecondAnswer extends Model
{
    use HasFactory;

    protected $fillable=[
        'one_second_result_id',
        'question_id',
        'audio_path',
        'audio_text',
    ];
}
