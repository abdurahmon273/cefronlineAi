<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpeakingPartThreeQuestions extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable=[
        'question',
        'part_three_id',
        'type',
        'audio_url',
    ];

    public const TYPES=[
        '0'=>'For',
        '1'=>'Against',
    ];
}
