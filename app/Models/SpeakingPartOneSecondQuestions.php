<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpeakingPartOneSecondQuestions extends Model
{
    use HasFactory,softDeletes;

    protected $fillable=[
        'question',
        'time',
        'modal_answer',
        'part_one_second_id',
        'audio_url'
    ];
}
