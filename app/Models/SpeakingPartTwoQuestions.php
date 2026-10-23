<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpeakingPartTwoQuestions extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable=[
        'question',
        'part_two_id',
        'audio_url'
    ];
}
