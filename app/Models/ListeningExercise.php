<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ListeningExercise extends Model
{
    use HasFactory,softDeletes;

    protected $fillable=[
        'name',
        'part_category',
        'status',
        'difficulty',
        'time_limit',
        'order_id',
        'exercise_id',
        'created_at',
        'deleted_at',
    ];

    public  const  PART_CATEGORIES=[
        '1'=>'part 1',
        '2'=>'part 2',
        '3'=>'part 3',
        '4'=>'part 4',
        '5'=>'part 5',
        '6'=>'part 6',
        '7'=>'full'
    ];
    public  const  DIFFICULTIES=[
        '0'=>'easy',
        '1'=>'medium',
        '2'=>'difficult',
    ];

    public const STATUS = [
        '1' => 'premium',
        '0' => 'free',
    ];

    public const PART_LINKS = [
        '1'=>'admin.listenings.part1.index',
        '2'=>'admin.listenings.part2.index',
        '3'=>'admin.listenings.part3.index',
        '4'=>'admin.listenings.part4.index',
        '5'=>'admin.listenings.part5.index',
        '6'=>'admin.listenings.part6.index',
        '7'=>'admin.listenings.full_exercise.index',
    ];

    public function question()
    {
        if ($this->listening_part_one_fife_question){
            return $this->hasOne(ListeningPartOneFifeQuestion::class,'listening_exercise_id');
        }elseif ($this->listening_part_two_six_question){
            return $this->hasOne(ListeningPartTwoSixQuestion::class,'listening_exercise_id');
        }elseif ($this->listening_part_three_four){
            return $this->hasOne(ListeningPartThreeFourQuestion::class,'listening_exercise_id');
        }
    }
    public function listening_part_one_fife_question()
    {
        return $this->hasOne(ListeningPartOneFifeQuestion::class,'listening_exercise_id');
    }
    public function listening_part_two_six_question()
    {
        return $this->hasOne(ListeningPartTwoSixQuestion::class,'listening_exercise_id');
    }
    public function listening_part_three_four()
    {
        return $this->hasOne(ListeningPartThreeFour::class,'listening_exercise_id');
    }

    public function part_one()
    {
        return $this->hasOne(ListeningPartOneFifeQuestion::class,'listening_exercise_id')->where('type','0');
    }
    public function part_two()
    {
        return $this->hasOne(ListeningPartTwoSixQuestion::class,'listening_exercise_id')->where('type','0');
    }
    public function part_three()
    {
        return $this->hasOne(ListeningPartThreeFour::class,'listening_exercise_id')->where('type','0');
    }
    public function part_four()
    {
        return $this->hasOne(ListeningPartThreeFour::class,'listening_exercise_id')->where('type','1');
    }
    public function part_five()
    {
        return $this->hasOne(ListeningPartOneFifeQuestion::class,'listening_exercise_id')->where('type','1');
    }

    public function part_six()
    {
        return $this->hasOne(ListeningPartTwoSixQuestion::class,'listening_exercise_id')->where('type','1');
    }

    public function part_one_api()
    {
        return $this->hasOne(ListeningPartOneFifeQuestion::class, 'listening_exercise_id')
            ->where('type', '0')
            ->whereHas('tests', function ($query) {
                $query->groupBy('listening_part_one_fife_question_id')
                    ->havingRaw('COUNT(*) > 7');
            });
    }

    public function full_exercise()
    {
        return $this->hasOne(FullExercise::class,'listening_exercise_id');
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class,'exercise_id');
    }
    public static function updateOrder($data)
    {
        foreach ($data as $datum) {
            $id = $datum['value'];
            $displayIndex = $datum['order'];

            static::where('id', $id)->update(['order_id' => $displayIndex]);
        }
    }
}
