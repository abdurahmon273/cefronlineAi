<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReadingPartFourTest extends Model
{
    use HasFactory,softDeletes;

    protected $fillable = [
        'question',
        'answer_1',
        'answer_2',
        'answer_3',
        'answer_4',
        'type', // 1,2
        'correct_answer', // 1,2,3,4
        'reading_part_four_id'
    ];

    public const types = [
        '1'=>'TFNg',
        '2'=>'MCHQ',
    ];

    public function part_four()
    {
        return $this->belongsTo(ReadingPartFour::class,'reading_part_four_id');
    }
}
