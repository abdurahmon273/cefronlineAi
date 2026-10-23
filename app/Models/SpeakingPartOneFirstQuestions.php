<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpeakingPartOneFirstQuestions extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable=[
        'question',
        'time',
        'modal_answer',
        'part_one_first_id',
        'audio_url',
    ];
}
