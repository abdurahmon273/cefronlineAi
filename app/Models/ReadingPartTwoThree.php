<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReadingPartTwoThree extends Model
{
    use HasFactory,softDeletes;

    protected $fillable=[
        'advice',
        'type',
        'reading_exercise_id'
    ];

    public const TYPES = [
        '0'=>'part 2',
        '1'=>'part 3',
    ];

    public function texts()
    {
        return $this->hasMany(ReadingPartTwoThreeTexts::class,'reading_part_two_three_id');
    }
    public function answers()
    {
        return $this->hasMany(ReadingPartTwoThreeAnswer::class,'reading_part_two_three_id');
    }
}
