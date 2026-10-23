<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartOneFiveAnswer extends Model
{
    use HasFactory,softDeletes;

    protected $fillable=[
        'reading_result_id',
        'type',
        'is_correct',
        'answer',
        'answer_id',
        'correct_answer',
    ];


}
