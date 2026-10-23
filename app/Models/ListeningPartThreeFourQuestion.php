<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListeningPartThreeFourQuestion extends Model
{
    use HasFactory;

    protected $fillable=[
        'title',
        'correct_answer_id',
        'listening_part_three_four_id',
    ];

    public function correct_answer()
    {
        return $this->belongsTo(ListeningPartThreeFourAnswers::class , 'correct_answer_id');
    }

}
