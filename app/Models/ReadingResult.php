<?php

namespace App\Models;

use App\Enums\CefrTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ReadingResult extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_type',
        'all_answers',
        'correct_answers',
        'wrong_answers',
        'percent',
        'reading_exercise_id',
        'reading_draft',
    ];
    protected $casts = [
        'reading_draft' => 'array',
    ];


    public const CATEGORY_TYPES=[
        '1'=>'part 1',
        '2'=>'part 2',
        '3'=>'part 3',
        '4'=>'part 4',
        '5'=>'part 5',
        '6'=>'full'
    ];
    protected $appends = ['percentage_data'];

    public function percentageData(): Attribute
    {
        return Attribute::get(function () {
            $all     = (int) ($this->all_answers ?? 0);
            $correct = (int) ($this->correct_answers ?? 0);

            $radius = 46;
            $circumference = round(2 * pi() * $radius); // ≈ 289

            if ($all <= 0) {
                $mock_ball = null;
                $is_mock = (int) optional($this->exercise)->part_category === 6;

                if ($is_mock) {
                    $mock_ball = CefrTypeEnum::READING_BALL[$correct] ?? null;
                }

                return [
                    'percent'   => 0,
                    'array'     => $circumference, // 289
                    'offset'    => $circumference, // 289
                    'mock_ball' => $mock_ball,
//                    'degree'    => $this->generateDegree($mock_ball),
                    'is_mock'   => $is_mock,
                ];
            }

            $percent = round(($correct / $all) * 100);

            $offset = round(
                $circumference - ($circumference * $percent / 100)
            );

            $mock_ball = null;
            $is_mock = (int) optional($this->exercise)->part_category === 6;

            if ($is_mock) {
                $mock_ball = CefrTypeEnum::READING_BALL[$correct] ?? null;
            }

            return [
                'percent'   => $percent,        // masalan: 72
                'array'     => $circumference,  // 289
                'offset'    => $offset,         // masalan: 81
                'mock_ball' => $mock_ball,
//                'degree'    => $this->generateDegree($mock_ball),
                'is_mock'   => $is_mock,
            ];
        });
    }

    public function PartOneAnswers()
    {
        return $this->hasMany(PartOneFiveAnswer::class , 'reading_result_id')->where('type',0);
    }
    public function PartTwoAnswers()
    {
        return $this->hasMany(PartTwoThreeAnswer::class , 'reading_result_id')->where('type',0);
    }
    public function PartThreeAnswers()
    {
        return $this->hasMany(PartTwoThreeAnswer::class , 'reading_result_id')->where('type',1);
    }
    public function PartFourAnswers()
    {
        return $this->hasMany(PartFourFiveAnswer::class , 'reading_result_id')->where('type',0);
    }
    public function PartFiveMCHQAnswers()
    {
        return $this->hasMany(PartFourFiveAnswer::class , 'reading_result_id')->where('type',1);
    }
    public function PartFiveFTHGAnswers()
    {
        return $this->hasMany(PartOneFiveAnswer::class , 'reading_result_id')->where('type',1);
    }

    public function exercise()
    {
        return $this->belongsTo(ReadingExercise::class, 'reading_exercise_id');
    }

    public function full_results()
    {
        return $this->hasMany(FullResult::class, 'reading_result_id');
    }
}
