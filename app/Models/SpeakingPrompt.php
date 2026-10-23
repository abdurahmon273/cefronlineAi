<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpeakingPrompt extends Model
{
    use HasFactory;

    protected $fillable=[
        'type',
        'input_prompt_text',
        'output_prompt_text',
    ];

    public const TYPES=[
        1=>'Speaking Part 1.1',
        2=>'Speaking Part 1.2',
        3=>'Speaking Part 2',
        4=>'Speaking Part 3',
    ];
}
