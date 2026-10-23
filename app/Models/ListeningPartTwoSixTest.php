<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ListeningPartTwoSixTest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'count_number',
        'correct_answer',
        'extra_answer',
        'listening_part_two_six_question_id'
    ];

    public const CORRECT_ANSWERS = [
        '1'=>'Answer 1',
        '2'=>'Answer 2',
        '3'=>'Answer 3',
    ];
}
