<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReadingPartTwoThreeAnswer extends Model
{
    use HasFactory,softDeletes;

    protected $fillable=[
        'letter',
        'answer',
        'reading_part_two_three_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    public const LETTERS = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N'];

    public function text()
    {
        return $this->hasOne(ReadingPartTwoThreeTexts::class,'correct_answer_id');
    }
}
