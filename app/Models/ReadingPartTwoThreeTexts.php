<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReadingPartTwoThreeTexts extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable=[
        'text',
        'correct_answer_id',
        'reading_part_two_three_id',
    ];

    public function correct_answer()
    {
        return $this->belongsTo(ReadingPartTwoThreeAnswer::class, 'correct_answer_id')->withTrashed();
    }
}
