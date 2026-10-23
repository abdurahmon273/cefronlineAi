<?php

namespace App\Models;

use App\Enums\CefrTypeEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class   WritingResult extends Model
{
    protected $with = ['partOneAnswer'];
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_type',
        'process_category_type',
        'text_1',
        'text_2',
        'writing_exercise_id',
        'user_id',
        'status',
        'with_ai',
        'score',
        'max_score',
        'ball',
        'degree',
        'ai_check_price',
        'uzs_ai_check_price',
    ];

    public const CATEGORY_TYPES = [
        '1'=>'Task 1',
        '2'=>'Task 2',
        '3'=>'Full',
    ];

    public const STATUS = [
        '0'=>'not_ai',
        '1'=>'ai_processing',
        '2'=>'ai_responding',
        '3'=>'Checked',
        '4'=>'fail',
    ];

    public function partOneAnswer()
    {
        return $this->hasOne(WritingPartOneAnswer::class ,'writing_result_id');
    }

    public function partTwoAnswer()
    {
      return $this->hasOne(WritingPartTwoAnswer::class ,'writing_result_id');
    }
    public function percentageData(): Attribute
    {
        return Attribute::get(function () {

            $all     = (int) ($this->max_score ?? 0); // all_answers kabi
            $score=$this->partOneAnswer?->score_1+$this->partOneAnswer?->score_2 + $this->partTwoAnswer?->score;
            $correct=0;
            $correct = (int) (CefrTypeEnum::WRITING_BALL[$this->category_type][rtrim(rtrim(number_format($score, 1, '.', ''), '0'), '.') ] ?? 0);     // correct_answers kabi


            $radius = 46;
            $circumference = (int) round(2 * pi() * $radius); // ≈ 289

            if ($all <= 0) {
                return [
                    'percent' => 0,
                    'array'   => $circumference,
                    'offset'  => $circumference,
                    'ball'    => 0,
                ];
            }

            if ($correct < 0) $correct = 0;
            if ($correct > $all) $correct = $all;

            $percent = (int) round(($correct / $all) * 100);

            $offset = (int) round(
                $circumference - ($circumference * $percent / 100)
            );

            return [
                'percent' => $percent,
                'array'   => $circumference,
                'offset'  => $offset,
                'ball'    => $correct,
            ];
        });
    }

    public function exercise()
    {
        return $this->belongsTo(WritingExercise::class, 'writing_exercise_id');
    }


    public function full_results()
    {
        return $this->hasMany(FullResult::class, 'writing_result_id');
    }
}
