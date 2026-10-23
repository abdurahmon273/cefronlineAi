<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WritingPartOneAnswer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'text_1',
        'text_2',
        'writing_result_id',
        'score_1',
        'score_2',
        'result_1_json',
        'result_2_json',
        'max_score',
    ];

}
