<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ListeningResultPartThreeFourAnswers extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable=[
        'listening_result_id',
        'type',
        'is_correct',
        'question_id',
        'answer_id',
        'correct_answer',
        'correct_answer_id',
    ];
}
