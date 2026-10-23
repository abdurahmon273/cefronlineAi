<?php

namespace App\Http\Resources\Listenings;

use App\Http\Resources\Full\FullExerciseResource;
use App\Http\Resources\Reading\ReadingExercisesResource;
use App\Http\Resources\Speaking\SpeakingExerciseResource;
use App\Http\Resources\Writing\WritingExerciseResource;
use App\Models\Exercise;
use App\Models\FullExercise;
use App\Models\ListeningExercise;
use App\Models\ReadingExercise;
use App\Models\SpeakingExercise;
use App\Traits\PrepareTextTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExerciseResource extends JsonResource
{
    use PrepareTextTrait;
    public function toArray(Request $request): array
    {


        if ($this->category_type=='0'){

            if (count($this->listening_exercises)>=0){
                $listening_exercises = $this->listening_exercises()
                    ->where('exercise_id', $this->id) // Faqat o'z exercise_id ni tekshirish
                    ->where(function ($query) {
                        $query->whereHas('part_one', function ($subQuery) {
                            $subQuery->whereHas('tests', function ($innerQuery) {
                                $innerQuery->groupBy('listening_part_one_fife_question_id')
                                    ->havingRaw('COUNT(*) >= 1');
                            });
                        })
                            ->orWhereHas('part_two', function ($subQuery) {
                                $subQuery->whereHas('listening_part_two_six_tests', function ($innerQuery) {
                                    $innerQuery->groupBy('listening_part_two_six_question_id')
                                        ->havingRaw('COUNT(*) >= 1');
                                });
                            })
                            ->orWhereHas('part_three', function ($subQuery) {
                                $subQuery->whereHas('part_three_four_questions', function ($innerQuery) {
                                    $innerQuery->groupBy('listening_part_three_four_id')
                                        ->havingRaw('COUNT(*) >= 1');
                                });
                            })
                            ->orWhereHas('part_four', function ($subQuery) {
                                $subQuery->whereHas('part_three_four_questions', function ($innerQuery) {
                                    $innerQuery->groupBy('listening_part_three_four_id')
                                        ->havingRaw('COUNT(*) >= 1');
                                });
                            })
                            ->orWhereHas('part_five', function ($subQuery) {
                                $subQuery->whereHas('tests', function ($innerQuery) {
                                    $innerQuery->groupBy('listening_part_one_fife_question_id')
                                        ->havingRaw('COUNT(*) >= 1');
                                });
                            })
                            ->orWhereHas('part_six', function ($subQuery) {
                                $subQuery->whereHas('listening_part_two_six_tests', function ($innerQuery) {
                                    $innerQuery->groupBy('listening_part_two_six_question_id')
                                        ->havingRaw('COUNT(*) >= 1');
                                });
                            });
                    })
                    ->orderBy('order_id')
                    ->get();

//                ListeningExercise::where('exercise_id',$this->id)
                      // Make sure to call get() or similar to fetch the results

                return  [
                    'id'=>$this->id,
                    'title'=>$this->name,
                    'exercise_count'=>count($listening_exercises),
                    'listening_exercises'=>ListeningExerciseResource::collection($listening_exercises),
                ];
            }else{
                return  [
                    'id'=>$this->id,
                    'title'=>$this->name,
                    'exercise_count'=>0,
                    'listening_exercises'=>[],
                ];
            }

        }
        elseif ($this->category_type=='1'){
            $reading_exercises = $this->reading_exercises()
                ->where('exercise_id', $this->id) // Faqat o'z exercise_id ni tekshirish
                ->where(function ($query) {
                    $query->whereHas('part_one', function ($subQuery) {
                        $subQuery->whereHas('answers', function ($innerQuery) {
                            $innerQuery->groupBy('reading_part_one_five_question_id')
                                ->havingRaw('COUNT(*) >= 1');
                        });
                    })
                        ->orWhereHas('part_two', function ($subQuery) {
                            $subQuery->whereHas('texts', function ($innerQuery) {
                                $innerQuery->groupBy('reading_part_two_three_id')
                                    ->havingRaw('COUNT(*) >= 1');
                            });
                        })
                        ->orWhereHas('part_three', function ($subQuery) {
                            $subQuery->whereHas('texts', function ($innerQuery) {
                                $innerQuery->groupBy('reading_part_two_three_id')
                                    ->havingRaw('COUNT(*) >= 1');
                            });
                        })
                        ->orWhereHas('part_four', function ($subQuery) {
                            $subQuery->whereHas('tests', function ($innerQuery) {
                                $innerQuery->groupBy('reading_part_four_id')
                                    ->havingRaw('COUNT(*) >= 1');
                            });
                        })
                        ->orWhereHas('part_five', function ($subQuery) {
                            $subQuery->whereHas('answers', function ($innerQuery) {
                                $innerQuery->groupBy('reading_part_one_five_question_id')
                                    ->havingRaw('COUNT(*) >= 1');
                            });
                        });
                })
                ->orderBy('order_id')
                ->get();

            return  [
                'id'=>$this->id,
                'title'=>$this->name,
                'exercise_count'=>count($reading_exercises),
                'reading_exercises'=>ReadingExercisesResource::collection($reading_exercises),
            ];
        }
        elseif ($this->category_type=='2'){
            $writing_exercise=$this->writing_exercises;

            return  [
                'id'=>$this->id,
                'title'=>$this->name,
                'part_type'=>Exercise::PART_TYPES[$this->part_type],
                'exercise_count'=>count($writing_exercise),
                'task_1_prepare_text'=>$this->GetPrepareTextByType(1),
                'task_2_prepare_text'=>$this->GetPrepareTextByType(2),
                'writing_exercises'=>WritingExerciseResource::collection($writing_exercise),
            ];
        }
        elseif ($this->category_type=='3'){
            $speaking_exercise=SpeakingExercise::where('exercise_id',$this->id)
                ->with('part_one_first.questions','part_one_second.questions','part_two.questions','part_three.questions')
               ->where(function ($query1) {
                   $query1->whereHas('part_one_first', function ($query) {
                       $query->whereHas('questions', function ($subQuery) {
                           $subQuery->groupBy('part_one_first_id')
                               ->havingRaw('COUNT(*) >= 1');
                       });
                   })->orwhereHas('part_two', function ($query) {
                       $query->whereHas('questions', function ($subQuery) {
                           $subQuery->groupBy('part_two_id')
                               ->havingRaw('COUNT(*) >= 1');
                       });
                   })->orwhereHas('part_three');
               })
                ->orderBy('order_id')
                ->get();

                return  [
                    'id'=>$this->id,
                    'title'=>$this->name,
                    'exercise_count'=>count($speaking_exercise),
                    'part_1_prepare_text'=>$this->GetPrepareTextByType(3),
                    'part_2_prepare_text'=>$this->GetPrepareTextByType(4),
                    'part_3_prepare_text'=>$this->GetPrepareTextByType(5),
                    'speaking_exercises'=>SpeakingExerciseResource::collection($speaking_exercise),
                ];
            }


        elseif ($this->category_type=='4'){

            $full_exercise =  FullExercise::with(
                'listening_exercise.part_one',
                'reading_exercise','speaking_exercise.part_one_first.questions',
                'speaking_exercise.part_one_second.questions',
                'speaking_exercise.part_two.questions',
                'speaking_exercise.part_three.questions',
                'writing_exercise.task_one',
                'writing_exercise.task_two')
                ->where('exercise_id', $this->id)
                ->where(function ($query) {
                    $query ->whereHas('listening_exercise', function ($query) {
                        $query->with('part_one')->whereHas('part_one', function ($query) {
                            $query->whereHas('tests', function ($subQuery) {
                                $subQuery->groupBy('listening_part_one_fife_question_id')
                                    ->havingRaw('COUNT(*) >= 1');
                            });
                        })->whereHas('part_two', function ($query) {
                            $query->whereHas('listening_part_two_six_tests', function ($subQuery) {
                                $subQuery->groupBy('listening_part_two_six_question_id')
                                    ->havingRaw('COUNT(*) >= 1');
                            });
                        })->whereHas('part_three', function ($query) {
                            $query->whereHas('part_three_four_questions', function ($subQuery) {
                                $subQuery->groupBy('listening_part_three_four_id')
                                    ->havingRaw('COUNT(*) >= 1');
                            });
                        })->whereHas('part_five', function ($query) {
                            $query->whereHas('tests', function ($subQuery) {
                                $subQuery->groupBy('listening_part_one_fife_question_id')
                                    ->havingRaw('COUNT(*) >= 1');
                            });
                        })->whereHas('part_six', function ($query) {
                            $query->whereHas('listening_part_two_six_tests', function ($subQuery) {
                                $subQuery->groupBy('listening_part_two_six_question_id')
                                    ->havingRaw('COUNT(*) >= 1');
                            });
                        });
                    })
                        ->orwhereHas('reading_exercise', function ($query) {
                            $query->whereHas('part_one', function ($query) {
                                $query->whereHas('answers', function ($subQuery) {
                                    $subQuery->groupBy('reading_part_one_five_question_id')
                                        ->havingRaw('COUNT(*) >= 1');
                                });
                            })->whereHas('part_two', function ($query) {
                                $query->whereHas('texts', function ($subQuery) {
                                    $subQuery->groupBy('reading_part_two_three_id')
                                        ->havingRaw('COUNT(*) >= 1');
                                });
                            })->whereHas('part_three', function ($query) {
                                $query->whereHas('texts', function ($subQuery) {
                                    $subQuery->groupBy('reading_part_two_three_id')
                                        ->havingRaw('COUNT(*) >= 1');
                                });
                            })->whereHas('part_four', function ($query) {
                                $query->whereHas('tests', function ($subQuery) {
                                    $subQuery->groupBy('reading_part_four_id')
                                        ->havingRaw('COUNT(*) >= 1');
                                });
                            })->whereHas('part_five', function ($query) {
                                $query->whereHas('answers', function ($subQuery) {
                                    $subQuery->groupBy('reading_part_one_five_question_id')
                                        ->havingRaw('COUNT(*) >= 1');
                                });
                            });
                        })
                        ->orwhereHas('speaking_exercise', function ($query) {
                            $query->whereHas('part_one_first', function ($query) {
                                $query->whereHas('questions', function ($subQuery) {
                                    $subQuery->groupBy('part_one_first_id')
                                        ->havingRaw('COUNT(*) >= 1');
                                });
                            })->whereHas('part_one_second', function ($query) {
                                $query->whereHas('questions', function ($subQuery) {
                                    $subQuery->groupBy('part_one_second_id')
                                        ->havingRaw('COUNT(*) >= 1');
                                })->whereHas('images', function ($subQuery) {
                                    $subQuery->groupBy('part_one_second_id')
                                        ->havingRaw('COUNT(*) >= 1');
                                });
                            })->whereHas('part_two', function ($query) {
                                $query->whereHas('questions', function ($subQuery) {
                                    $subQuery->groupBy('part_two_id')
                                        ->havingRaw('COUNT(*) >= 1');
                                })->whereHas('images', function ($subQuery) {
                                    $subQuery->groupBy('part_two_id')
                                        ->havingRaw('COUNT(*) >= 1');
                                });
                            })->whereHas('part_three', function ($subQuery) {
                                $subQuery->whereNotNull('question_text'); // Check only for question_text
                            });
                        })
                        ->orwhereHas('writing_exercise',function ($query){
                            $query->whereHas('task_one')->whereHas('task_two');
                        });
                })
                ->orderBy('order_id')
                ->get();


            return  [
                'id'=>$this->id,
                'title'=>$this->name,
                'exercise_count'=>count($full_exercise),
                'full_exercises'=>FullExerciseResource::collection($full_exercise),
            ];

        }else{
            return [];
        }

    }
}
