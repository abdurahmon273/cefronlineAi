<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrepareText extends Model
{
    use HasFactory;

    public const TYPES=[
        1=>'Writing Task 1',
        2=>'Writing Task 2',
        3=>'Speaking Part 1',
        4=>'Speaking Part 2',
        5=>'Speaking Part 3',
    ];

    protected $fillable = [
        'type',
        'text',
        'deleted_at'
    ];
}
