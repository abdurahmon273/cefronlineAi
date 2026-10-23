<?php

namespace App\Models;

use App\Enums\CefrTypeEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpeakingResult extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_type',
        'process_category_type',
        'speaking_exercise_id',
        'audio_path',
        'with_ai',
        'status',
        'score',
        'max_score',
        'ai_check_price',
        'uzs_ai_check_price',
    ];

    public const CATEGORY_TYPES=[
        '1'=>'Part 1',
        '2'=>'Part 2',
        '3'=>'Part 3',
        '4'=>'Full',
    ];

    public const STATUS=[
        '0'=>'not_ai',
        '1'=>'ai_preparing',
        '2'=>'ai_checking',
        '3'=>'Checked',
        '4'=>'fail',
    ];

    public function part_one_first()
    {
       return $this->hasOne(SpeakingPartOneFirstResult::class, 'speaking_result_id', 'id');
    }
    public function part_one_second()
    {
        return $this->hasOne(SpeakingPartOneSecondResult::class, 'speaking_result_id', 'id');
    }
    public function part_two()
    {
        return  $this->hasOne(SpeakingPartTwoResult::class, 'speaking_result_id', 'id');
    }
    public function part_three()
    {
        return  $this->hasOne(SpeakingPartThreeResult::class, 'speaking_result_id', 'id');
    }

    public function speaking_exercise()
    {
        return $this->belongsTo(SpeakingExercise::class, 'speaking_exercise_id');
    }

    public function exercise()
    {
        return $this->belongsTo(SpeakingExercise::class, 'speaking_exercise_id');
    }

    public function percentageData(): Attribute
    {
        return Attribute::get(function () {

//            $all     = (int) ($this->max_score ?? 0); // all_answers kabi
            $all = CefrTypeEnum::SPEAKING_MAX_SCORES[$this->category_type];
            $correct=0;
            if ($this->category_type==1){
                $score1 = (float) optional($this->part_one_first)->score ?? 0;
                $score2 = (float) optional($this->part_one_second)->score ?? 0;

                $part_1_1 = rtrim(rtrim(number_format($score1, 1, '.', ''), '0'), '.');
                $part_1_2 = rtrim(rtrim(number_format($score2, 1, '.', ''), '0'), '.');

                $sum = rtrim(rtrim(number_format($part_1_1 + $part_1_2, 1, '.', ''), '0'), '.');

                $correct = CefrTypeEnum::SPEAKING_BALL[$this->category_type][$sum] ?? 0;
            }else{
                $correct = (int) (CefrTypeEnum::SPEAKING_BALL[$this->category_type][rtrim(rtrim(number_format($this->score, 1, '.', ''), '0'), '.') ] ?? 0);     // correct_answers kabi

            }

            if ($all <= 0) {
                return [
                    'percent' => 0,
                    'ball'    => 0,
                ];
            }
            if ($correct < 0) $correct = 0;
            if ($correct > $all) $correct = $all;

            $percent = (int) round(($correct / $all) * 100);

            return [
                'percent' => $percent,
                'ball'    => $correct,
            ];
        });
    }

    public function full_results()
    {
        return $this->hasMany(FullResult::class, 'speaking_result_id');
    }
}
