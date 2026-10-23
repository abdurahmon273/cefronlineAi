<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WritingPartTwoAnswer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'text',
        'writing_result_id',
        'score',
        'result_json',
        'max_score',
    ];
}
