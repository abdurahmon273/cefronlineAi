<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpeakingPartThreeResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'speaking_result_id',
        'score',
        'result_json',
        'part_three_id',
        'audio_path',
        'audio_text',
    ];

}
