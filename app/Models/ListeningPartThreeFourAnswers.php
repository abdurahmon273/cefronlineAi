<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListeningPartThreeFourAnswers extends Model
{
    use HasFactory;

    protected $fillable=[
        'letter',
        'answer',
        'listening_part_three_four_id',
    ];

    public const LETTERS = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N'];

    public function question()
    {
        return $this->hasOne(ListeningPartThreeFourQuestion::class,'correct_answer_id');
    }

    public function part_three_four()
    {
      return  $this->belongsTo(ListeningPartThreeFour::class,'listening_part_three_four_id');
    }

}
