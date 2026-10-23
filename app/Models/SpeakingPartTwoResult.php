<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpeakingPartTwoResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'speaking_result_id',
        'score',
        'result_json',
        'question_id',
        'audio_path',
        'audio_text',
    ];

}
