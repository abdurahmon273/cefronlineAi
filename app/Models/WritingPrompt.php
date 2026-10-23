<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WritingPrompt extends Model
{
    use HasFactory;

    protected $fillable=[
        'type',
        'input_prompt_text',
        'output_prompt_text',
    ];

    public const TYPES=[
        1=>'Writing Part 1.1',
        2=>'Writing Part 1.2',
        3=>'Writing Part 2',
    ];

}
