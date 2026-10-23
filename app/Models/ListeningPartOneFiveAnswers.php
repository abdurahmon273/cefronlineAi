<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ListeningPartOneFiveAnswers extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable=[
        'listening_result_id',
        'type',
        'is_correct',
        'answer_key',
        'correct_answer_key',
        'correct_answer',
        'test_id',
    ];
}
