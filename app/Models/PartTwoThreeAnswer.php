<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartTwoThreeAnswer extends Model
{
    use HasFactory,softDeletes;

    protected $fillable=[
        'reading_result_id',
        'type',
        'is_correct',
        'text_id',
        'answer_id',
        'correct_answer',
        'correct_answer_id',
    ];
}
