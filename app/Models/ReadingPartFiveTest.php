<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadingPartFiveTest extends Model
{
    use HasFactory;

    protected $fillable=[
        'question',
        'answer_1',
        'answer_2',
        'answer_3',
        'answer_4',
        'correct_answer', // 1,2,3,4
        'reading_part_one_five_question_id'
    ];

    public function part_five()
    {
        return $this->belongsTo(ReadingPartOneFiveQuestion::class, 'reading_part_one_five_question_id');
    }
}
