<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Enums\CefrTypeEnum;
class ListeningResult extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_type',
        'all_answers',
        'correct_answers',
        'wrong_answers',
        'percent',
        'listening_exercise_id'
    ];

    public const CATEGORY_TYPES=[
        '1'=>'part 1',
        '2'=>'part 2',
        '3'=>'part 3',
        '4'=>'part 4',
        '5'=>'part 5',
        '6'=>'part 6',
        '7'=>'full'
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
        return $this->hasMany(ListeningPartOneFiveAnswers::class , 'listening_result_id')->where('type',0);
    }
    public function PartTwoAnswers()
    {
        return $this->hasMany(ListeningPartTwoSixAnswers::class , 'listening_result_id')->where('type',0);
    }
    public function PartThreeAnswers()
    {
        return $this->hasMany(ListeningResultPartThreeFourAnswers::class , 'listening_result_id')->where('type',0);
    }
    public function PartFourAnswers()
    {
        return $this->hasMany(ListeningResultPartThreeFourAnswers::class , 'listening_result_id')->where('type',1);
    }

    public function PartThreeFourAnswers()
    {
        return $this->hasMany(ListeningResultPartThreeFourAnswers::class , 'listening_result_id');
    }
    public function PartFiveAnswers()
    {
        return $this->hasMany(ListeningPartOneFiveAnswers::class , 'listening_result_id')->where('type',1);
    }
    public function PartSixAnswers()
    {
        return $this->hasMany(ListeningPartTwoSixAnswers::class , 'listening_result_id')->where('type',1);
    }

    public function exercise()
    {
        return $this->belongsTo(ListeningExercise::class, 'listening_exercise_id');
    }

    protected static function booted()
    {
        static::deleting(function (self $result) {
            $method = $result->isForceDeleting() ? 'forceDelete' : 'delete';

            $result->PartOneAnswers()->{$method}();
            $result->PartTwoAnswers()->{$method}();
            $result->PartThreeFourAnswers()->{$method}();
            $result->PartFiveAnswers()->{$method}();
            $result->PartSixAnswers()->{$method}();
        });
    }

    public function full_results()
    {
        return $this->hasMany(FullResult::class, 'listening_result_id');
    }
}
