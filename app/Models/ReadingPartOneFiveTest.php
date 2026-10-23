<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReadingPartOneFiveTest extends Model
{
    use HasFactory,softDeletes;

    protected $fillable=[
        'count_number',
        'extra_answer',
        'correct_answer',
        'reading_part_one_five_question_id'
    ];

}
