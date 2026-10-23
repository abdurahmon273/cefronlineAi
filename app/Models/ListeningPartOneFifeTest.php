<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ListeningPartOneFifeTest extends Model
{
    use HasFactory,softDeletes;

    protected $fillable = [
        'question',
        'answer_1',
        'answer_2',
        'answer_3',
        'correct_answer', // 1,2,3
        'listening_part_one_fife_question_id'
    ];

    public const CORRECT_ANSWERS = [
        '1'=>'Answer 1',
        '2'=>'Answer 2',
        '3'=>'Answer 3',
    ];

    public function question()
    {
        return $this->belongsTo(ListeningPartOneFifeQuestion::class,'listening_part_one_fife_question_id');
    }
}
