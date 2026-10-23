<?php

namespace App\Traits;


use App\Enums\CefrTypeEnum;
use App\Models\FullResult;
use App\Models\ListeningPartOneFiveAnswers;
use App\Models\ListeningPartThreeFourAnswers;
use App\Models\ListeningPartTwoSixAnswers;
use App\Models\ListeningResultPartThreeFourAnswers;
use App\Models\PartFourFiveAnswer;
use App\Models\PartOneFiveAnswer;
use App\Models\PartTwoThreeAnswer;

trait TestStoreHelperTrait
{
    public function getDraftData($request)
    {
        $draft = $request->input('reading_draft') ?? $request->input('listening_draft');
        return json_decode($draft ?? '[]', true);
    }

    public function processParts($readingExercise, $draftData)
    {
        $validatedData = [];
        $totalCorrect = $wrongAnswers = $totalQuestions  = 0;

        if ($readingExercise->part_one){
            $partOne = $readingExercise->part_one()->with('answers')->first();
            $totalQuestions+=$partOne->answers->count();

            if (isset($draftData['part_one']) && isset($draftData['part_one']['question_data'])){
                $partOneValidatedData=[];


                foreach ($draftData['part_one']['question_data'] as $key=>$answer){
                    $thisAnswer=$partOne->answers->where('id',$answer['id'])->first();

                    if ($thisAnswer){
                        $normalize = fn ($value) =>
                        strtolower(trim(preg_replace('/\s+/', ' ', $value)));
                        $correctAnswers = [$thisAnswer->correct_answer];

                        if (!empty($thisAnswer->extra_answer)) {
                            $correctAnswers[] = $thisAnswer->extra_answer;
                        }
                        $isCorrect = in_array(
                            $normalize($answer['value']),
                            array_map($normalize, $correctAnswers),
                            true
                        ) ? 1 : 0;


                        $partOneValidatedData[]=[
                            'id'=>$thisAnswer->id,
                            'type'=>0,
                            'answer'=>$answer['value'],
                            'correct_answer'=>$thisAnswer->correct_answer,
                            'extra_answer'=>$thisAnswer->extra_answer,
                            'is_correct'=>$isCorrect,
                        ];
                        if ($isCorrect){$totalCorrect++;}else{$wrongAnswers++;}
                    }
                }


                $validatedData['part_one']=$partOneValidatedData;

            }else{
                $validatedData['part_one']=[];
            }

        }


        if ($readingExercise->part_two){
            $partTwo = $readingExercise->part_two()->with(['answers','texts'])->first();
            $totalQuestions+=$partTwo->texts->count();

            if (isset($draftData['part_two']) && isset($draftData['part_two']['question_data'])) {
                $partTwoValidatedData = [];

                foreach ($draftData['part_two']['question_data'] as $key=>$answer){
                    $thisText=$partTwo->texts->where('id',$answer['id'])->first();

                    if ($thisText){

                        $isCorrect=$thisText->correct_answer_id== $answer['value'] ? 1 : 0;
                        $correct_answer=$partTwo->answers->where('id',$thisText->correct_answer_id)->first();

                        if ($answer['value']){
                            $partTwoValidatedData[]=[
                                'id'=>$thisText->id,
                                'type'=>0,
                                'answer'=>$answer['value'],
                                'correct_answer_id'=>$thisText->correct_answer_id,
                                'correct_answer'=>$correct_answer->answer,
                                'is_correct'=>$isCorrect,
                            ];
                        }


                        if ($isCorrect){$totalCorrect++;}else{$wrongAnswers++;}
                    }
                }


                $validatedData['part_two']=$partTwoValidatedData;
            }else{
                $validatedData['part_two']=[];
            }
        }

        if ($readingExercise->part_three){
            $partThree = $readingExercise->part_three()->with(['answers','texts'])->first();
            $totalQuestions+=$partThree->texts->count();

            if (isset($draftData['part_three']) && isset($draftData['part_three']['question_data'])) {
                $partThreeValidatedData = [];

                foreach ($draftData['part_three']['question_data'] as $key=>$answer){
                    $thisText=$partThree->texts->where('id',$answer['id'])->first();

                    if ($thisText){

                        $isCorrect=$thisText->correct_answer_id== $answer['value'] ? 1 : 0;
                        $correct_answer=$partThree->answers->where('id',$thisText->correct_answer_id)->first();

                        if ($answer['value']) {
                            $partThreeValidatedData[] = [
                                'id' => $thisText->id,
                                'type' => 1,
                                'answer' => $answer['value'],
                                'correct_answer_id' => $thisText->correct_answer_id,
                                'correct_answer' => $correct_answer->answer,
                                'is_correct' => $isCorrect,
                            ];
                        }
                        if ($isCorrect){$totalCorrect++;}else{$wrongAnswers++;}
                    }
                }

                $validatedData['part_three']=$partThreeValidatedData;
            }else{
                $validatedData['part_three']=[];
            }
        }

        if ($readingExercise->part_four){
            $partFour = $readingExercise->part_four()->with(['tests'])->first();
            $totalQuestions+=$partFour->tests->count();

            if (isset($draftData['part_four']) && isset($draftData['part_four']['question_data'])) {
                $partFourValidatedData = [];

                foreach ($draftData['part_four']['question_data'] as $key=>$test){

                    $thisTest=$partFour->tests->where('id',$test['id'])->first();

                    if ($thisTest){

                        $isCorrect=$this->getNumberPartFourTest($test['value'])== $thisTest->correct_answer ? 1 : 0;

                        $partFourValidatedData[]=[
                            'id'=>$thisTest->id,
                            'type'=>0,
                            'answer'=>$test['value'],
                            'correct_answer'=>$thisTest->{'answer_'.$thisTest->correct_answer},
                            'correct_answer_key'=>'answer_'.$thisTest->correct_answer,
                            'is_correct'=>$isCorrect,
                        ];
                        if ($isCorrect){$totalCorrect++;}else{$wrongAnswers++;}
                    }
                }


                $validatedData['part_four']=$partFourValidatedData;
            }else{
                $validatedData['part_four']=[];
            }
        }

        if ($readingExercise->part_five){
            $partFive = $readingExercise->part_five()->with(['tests','answers'])->first();
            $totalQuestions+=$partFive->answers->count()+$partFive->tests->count();

            if (isset($draftData['part_five']) && isset($draftData['part_five']['question_data'])) {
                $partFiveValidatedData = [];

                foreach ($draftData['part_five']['question_data'] as $key=>$question){
                    if ($question['type']=='input'){
                        $thisAnswer=$partFive->answers->where('id',$question['id'])->first();

                        if ($thisAnswer){

                            $normalize = fn ($value) =>
                            strtolower(trim(preg_replace('/\s+/', ' ', $value)));
                            $correctAnswers = [$thisAnswer->correct_answer];

                            if (!empty($thisAnswer->extra_answer)) {
                                $correctAnswers[] = $thisAnswer->extra_answer;
                            }
                            $isCorrect = in_array(
                                $normalize($question['value']),
                                array_map($normalize, $correctAnswers),
                                true
                            ) ? 1 : 0;

                            $partFiveValidatedData[]=[
                                'id'=>$thisAnswer->id,
                                'type'=>'input',
                                'answer'=>$question['value'],
                                'correct_answer'=>$thisAnswer->correct_answer,
                                'extra_answer'=>$thisAnswer->extra_answer,
                                'is_correct'=>$isCorrect,
                            ];
                            if ($isCorrect){$totalCorrect++;}else{$wrongAnswers++;}
                        }
                    }else{
                        $thisTest=$partFive->tests->where('id',$question['id'])->first();

                        if ($thisTest){

                            $isCorrect=$this->getNumberPartFourTest($question['value'])== $thisTest->correct_answer ? 1 : 0;

                            $partFiveValidatedData[]=[
                                'id'=>$thisTest->id,
                                'type'=>'test',
                                'answer'=>$question['value'],
                                'correct_answer'=>$thisTest->{'answer_'.$thisTest->correct_answer},
                                'correct_answer_key'=>'answer_'.$thisTest->correct_answer,
                                'is_correct'=>$isCorrect,
                            ];
                            if ($isCorrect){$totalCorrect++;}else{$wrongAnswers++;}
                        }
                    }

                }

                $validatedData['part_five']=$partFiveValidatedData;
            }else{
                $validatedData['part_five']=[];
            }
        }


        return [$validatedData, $totalCorrect, $wrongAnswers, $totalQuestions];

    }

    public function getNumberPartFourTest($text)
    {
        preg_match_all('/answer_(\d+)/', $text, $matches);

        return $matches[1][0];
    }

    public function createReadingResultParts($result,$validatedData)
    {
        $PartOneFiveDataForInsert = [];
        $PartTwoThreeDataForInsert = [];
        $PartFourFiveDataForInsert = [];

        if (isset($validatedData['part_one'])){
            foreach ($validatedData['part_one'] as $answer){
                $PartOneFiveDataForInsert[]=[
                    'reading_result_id'=>$result->id,
                    'type'=>0,
                    'is_correct'=>$answer['is_correct'],
                    'answer'=>$answer['answer'],
                    'answer_id'=>$answer['id'],
                    'correct_answer'=>$answer['correct_answer'],
                ];
            }
        }

        if (isset($validatedData['part_two'])){
            foreach ($validatedData['part_two'] as $answer){
                $PartTwoThreeDataForInsert[]=[
                    'reading_result_id'=>$result->id,
                    'type'=>0,
                    'is_correct'=>$answer['is_correct'],
                    'text_id'=>$answer['id'],
                    'answer_id'=>$answer['answer'],
                    'correct_answer'=>$answer['correct_answer'],
                    'correct_answer_id'=>$answer['correct_answer_id'],
                ];
            }
        }
        if (isset($validatedData['part_three'])){
            foreach ($validatedData['part_three'] as $answer){
                $PartTwoThreeDataForInsert[]=[
                    'reading_result_id'=>$result->id,
                    'type'=>1,
                    'is_correct'=>$answer['is_correct'],
                    'text_id'=>$answer['id'],
                    'answer_id'=>$answer['answer'],
                    'correct_answer'=>$answer['correct_answer'],
                    'correct_answer_id'=>$answer['correct_answer_id'],
                ];
            }
        }


        if (isset($validatedData['part_four'])){
            foreach ($validatedData['part_four'] as $answer){
                $PartFourFiveDataForInsert[]=[
                    'reading_result_id'=>$result->id,
                    'type'=>0,
                    'is_correct'=>$answer['is_correct'],
                    'answer_key'=>$answer['answer'],
                    'correct_answer_key'=>$answer['correct_answer_key'],
                    'correct_answer'=>$answer['correct_answer'],
                    'test_id'=>$answer['id'],
                ];
            }
        }
        if (isset($validatedData['part_five'])){
            foreach ($validatedData['part_five'] as $answer){
                if ($answer['type']=='input'){
                    $PartOneFiveDataForInsert[]=[
                        'reading_result_id'=>$result->id,
                        'type'=>1,
                        'is_correct'=>$answer['is_correct'],
                        'answer'=>$answer['answer'],
                        'answer_id'=>$answer['id'],
                        'correct_answer'=>$answer['correct_answer'],
                    ];
                }else{
                    $PartFourFiveDataForInsert[]=[
                        'reading_result_id'=>$result->id,
                        'type'=>1,
                        'is_correct'=>$answer['is_correct'],
                        'answer_key'=>$answer['answer'],
                        'correct_answer_key'=>$answer['correct_answer_key'],
                        'correct_answer'=>$answer['correct_answer'],
                        'test_id'=>$answer['id'],
                    ];
                }

            }
        }

        if (!empty($PartOneFiveDataForInsert)) {
            PartOneFiveAnswer::insert($PartOneFiveDataForInsert);
        }

        if (!empty($PartTwoThreeDataForInsert)) {
            PartTwoThreeAnswer::insert($PartTwoThreeDataForInsert);
        }

        if (!empty($PartFourFiveDataForInsert)) {
            PartFourFiveAnswer::insert($PartFourFiveDataForInsert);
        }

        return $validatedData;
    }

    public function createListeningResultParts($result,$validatedData)
    {
        $PartOneFiveDataForInsert = [];
        $PartTwoSixDataForInsert = [];
        $PartThreeFourDataForInsert = [];


        if (isset($validatedData['part_one'])){
            foreach ($validatedData['part_one'] as $answer){
                $PartOneFiveDataForInsert[]=[
                    'listening_result_id'=>$result->id,
                    'type'=>0,
                    'is_correct'=>$answer['is_correct'],
                    'answer_key'=>$answer['answer'],
                    'correct_answer_key'=>$answer['correct_answer_key'],
                    'correct_answer'=>$answer['correct_answer'],
                    'test_id'=>$answer['id'],
                ];
            }
        }


        if (isset($validatedData['part_two'])){
            foreach ($validatedData['part_two'] as $answer){
                $PartTwoSixDataForInsert[]=[
                    'listening_result_id'=>$result->id,
                    'type'=>0,
                    'is_correct'=>$answer['is_correct'],
                    'answer'=>$answer['answer'],
                    'answer_id'=>$answer['id'],
                    'correct_answer'=>$answer['correct_answer'],
                ];
            }
        }

        if (isset($validatedData['part_three'])){
            foreach ($validatedData['part_three'] as $answer){
                $PartThreeFourDataForInsert[]=[
                    'listening_result_id'=>$result->id,
                    'type'=>0,
                    'is_correct'=>$answer['is_correct'],
                    'question_id'=>$answer['id'],
                    'answer_id'=>$answer['answer_id'],
                    'correct_answer'=>$answer['correct_answer'],
                    'correct_answer_id'=>$answer['correct_answer_id'],
                ];
            }
        }

        if (isset($validatedData['part_four'])){
            foreach ($validatedData['part_four'] as $answer){
                $PartThreeFourDataForInsert[]=[
                    'listening_result_id'=>$result->id,
                    'type'=>1,
                    'is_correct'=>$answer['is_correct'],
                    'question_id'=>$answer['id'],
                    'answer_id'=>$answer['answer_id'],
                    'correct_answer'=>$answer['correct_answer'],
                    'correct_answer_id'=>$answer['correct_answer_id'],
                ];
            }
        }


        if (isset($validatedData['part_five'])){
            foreach ($validatedData['part_five'] as $answer){
                $PartOneFiveDataForInsert[]=[
                    'listening_result_id'=>$result->id,
                    'type'=>1,
                    'is_correct'=>$answer['is_correct'],
                    'answer_key'=>$answer['answer'],
                    'correct_answer_key'=>$answer['correct_answer_key'],
                    'correct_answer'=>$answer['correct_answer'],
                    'test_id'=>$answer['id'],
                ];
            }
        }

        if (isset($validatedData['part_six'])){
            foreach ($validatedData['part_six'] as $answer){
                $PartTwoSixDataForInsert[]=[
                    'listening_result_id'=>$result->id,
                    'type'=>1,
                    'is_correct'=>$answer['is_correct'],
                    'answer'=>$answer['answer'],
                    'answer_id'=>$answer['id'],
                    'correct_answer'=>$answer['correct_answer'],
                ];
            }
        }


        if (!empty($PartOneFiveDataForInsert)) {
            ListeningPartOneFiveAnswers::insert($PartOneFiveDataForInsert);
        }

        if (!empty($PartTwoSixDataForInsert)) {
            ListeningPartTwoSixAnswers::insert($PartTwoSixDataForInsert);
        }

        if (!empty($PartThreeFourDataForInsert)) {
            ListeningResultPartThreeFourAnswers::insert($PartThreeFourDataForInsert);
        }

        return $validatedData;
    }

    public function processPartsListening($listeningExercise, $draftData)
    {
        $validatedData = [];
        $totalCorrect = $wrongAnswers = $totalQuestions  = 0;



        if ($listeningExercise->part_one){
            $partOne = $listeningExercise->part_one()->with(['tests'])->first();
            $totalQuestions+=$partOne->tests->count();

            if (isset($draftData['part_one']) && isset($draftData['part_one']['question_data'])) {
                $partOneValidatedData = [];

                foreach ($draftData['part_one']['question_data'] as $key=>$test){

                    $thisTest=$partOne->tests->where('id',$test['id'])->first();

                    if ($thisTest){

                        $isCorrect=$this->getNumberPartFourTest($test['value'])== $thisTest->correct_answer ? 1 : 0;

                        $partOneValidatedData[]=[
                            'id'=>$thisTest->id,
                            'type'=>0,
                            'answer'=>$test['value'],
                            'correct_answer'=>$thisTest->{'answer_'.$thisTest->correct_answer},
                            'correct_answer_key'=>'answer_'.$thisTest->correct_answer,
                            'is_correct'=>$isCorrect,
                        ];
                        if ($isCorrect){$totalCorrect++;}else{$wrongAnswers++;}
                    }
                }


                $validatedData['part_one']=$partOneValidatedData;
            }else{
                $validatedData['part_one']=[];
            }


        }

        if ($listeningExercise->part_two){
            $partTwo = $listeningExercise->part_two()->with('answers')->first();
            $totalQuestions+=$partTwo->answers->count();

            if (isset($draftData['part_two']) && isset($draftData['part_two']['question_data'])){
                $partTwoValidatedData=[];


                foreach ($draftData['part_two']['question_data'] as $key=>$answer){
                    $thisAnswer=$partTwo->answers->where('id',$answer['id'])->first();

                    if ($thisAnswer){
                        $normalize = fn ($value) =>
                        strtolower(trim(preg_replace('/\s+/', ' ', $value)));
                        $correctAnswers = [$thisAnswer->correct_answer];

                        if (!empty($thisAnswer->extra_answer)) {
                            $correctAnswers[] = $thisAnswer->extra_answer;
                        }
                        $isCorrect = in_array(
                            $normalize($answer['value']),
                            array_map($normalize, $correctAnswers),
                            true
                        ) ? 1 : 0;


                        $partTwoValidatedData[]=[
                            'id'=>$thisAnswer->id,
                            'type'=>0,
                            'answer'=>$answer['value'],
                            'correct_answer'=>$thisAnswer->correct_answer,
                            'extra_answer'=>$thisAnswer->extra_answer,
                            'is_correct'=>$isCorrect,
                        ];
                        if ($isCorrect){$totalCorrect++;}else{$wrongAnswers++;}
                    }
                }


                $validatedData['part_two']=$partTwoValidatedData;

            }else{
                $validatedData['part_two']=[];
            }

        }

        if ($listeningExercise->part_three){
            $partThree = $listeningExercise->part_three()->with(['answers','questions'])->first();
            $totalQuestions+=$partThree->questions->count();
            if (isset($draftData['part_three']) && isset($draftData['part_three']['question_data'])) {
                $partThreeValidatedData = [];

                foreach ($draftData['part_three']['question_data'] as $key=>$answer){
                    $thisQuestion=$partThree->questions->where('id',$answer['id'])->first();

                    if ($thisQuestion){

                        $thisAnswerId=$partThree->answers->where('letter',$answer['value'])->first()->id;
                        $isCorrect=$thisQuestion->correct_answer_id== $thisAnswerId ? 1 : 0;
                        $correct_answer=$partThree->answers->where('id',$thisQuestion->correct_answer_id)->first();

                        $partThreeValidatedData[]=[
                            'id'=>$thisQuestion->id,
                            'type'=>0,
                            'answer'=>$answer['value'],
                            'answer_id'=>$thisAnswerId,
                            'correct_answer_id'=>$thisQuestion->correct_answer_id,
                            'correct_answer'=>$correct_answer->answer,
                            'is_correct'=>$isCorrect,
                        ];
                        if ($isCorrect){$totalCorrect++;}else{$wrongAnswers++;}
                    }
                }


                $validatedData['part_three']=$partThreeValidatedData;
            }else{
                $validatedData['part_three']=[];
            }
        }

        if ($listeningExercise->part_four){
            $partFour = $listeningExercise->part_four()->with(['answers','questions'])->first();
            $totalQuestions+=$partFour->questions->count();

            if (isset($draftData['part_four']) && isset($draftData['part_four']['question_data'])) {
                $partFourValidatedData = [];

                foreach ($draftData['part_four']['question_data'] as $key=>$answer){
                    $thisQuestion=$partFour->questions->where('id',$answer['id'])->first();

                    if ($thisQuestion){

                        $thisAnswerId=$partFour->answers->where('letter',$answer['value'])->first()->id;
                        $isCorrect=$thisQuestion->correct_answer_id== $thisAnswerId ? 1 : 0;
                        $correct_answer=$partFour->answers->where('id',$thisQuestion->correct_answer_id)->first();

                        $partFourValidatedData[]=[
                            'id'=>$thisQuestion->id,
                            'type'=>1,
                            'answer'=>$answer['value'],
                            'answer_id'=>$thisAnswerId,
                            'correct_answer_id'=>$thisQuestion->correct_answer_id,
                            'correct_answer'=>ListeningPartThreeFourAnswers::LETTERS[$correct_answer->letter],
                            'is_correct'=>$isCorrect,
                        ];
                        if ($isCorrect){$totalCorrect++;}else{$wrongAnswers++;}
                    }
                }


                $validatedData['part_four']=$partFourValidatedData;
            }else{
                $validatedData['part_four']=[];
            }
        }

        if ($listeningExercise->part_five){
            $partFive = $listeningExercise->part_five()->with(['tests'])->first();
            $totalQuestions+=$partFive->tests->count();

            if (isset($draftData['part_five']) && isset($draftData['part_five']['question_data'])) {
                $partFiveValidatedData = [];

                foreach ($draftData['part_five']['question_data'] as $key=>$test){
                    $thisTest=$partFive->tests->where('id',$test['id'])->first();


                    if ($thisTest){

                        $isCorrect=$this->getNumberPartFourTest($test['value'])== $thisTest->correct_answer ? 1 : 0;

                        $partFiveValidatedData[]=[
                            'id'=>$thisTest->id,
                            'type'=>1,
                            'answer'=>$test['value'],
                            'correct_answer'=>$thisTest->{'answer_'.$thisTest->correct_answer},
                            'correct_answer_key'=>'answer_'.$thisTest->correct_answer,
                            'is_correct'=>$isCorrect,
                        ];
                        if ($isCorrect){$totalCorrect++;}else{$wrongAnswers++;}
                    }
                }


                $validatedData['part_five']=$partFiveValidatedData;
            }else{
                $validatedData['part_five']=[];
            }


        }

        if ($listeningExercise->part_six){
            $partSix = $listeningExercise->part_six()->with('answers')->first();
            $totalQuestions+=$partSix->answers->count();

            if (isset($draftData['part_six']) && isset($draftData['part_six']['question_data'])){
                $partSixValidatedData=[];


                foreach ($draftData['part_six']['question_data'] as $key=>$answer){
                    $thisAnswer=$partSix->answers->where('id',$answer['id'])->first();

                    if ($thisAnswer){
                        $normalize = fn ($value) =>
                        strtolower(trim(preg_replace('/\s+/', ' ', $value)));
                        $correctAnswers = [$thisAnswer->correct_answer];

                        if (!empty($thisAnswer->extra_answer)) {
                            $correctAnswers[] = $thisAnswer->extra_answer;
                        }
                        $isCorrect = in_array(
                            $normalize($answer['value']),
                            array_map($normalize, $correctAnswers),
                            true
                        ) ? 1 : 0;


                        $partSixValidatedData[]=[
                            'id'=>$thisAnswer->id,
                            'type'=>1,
                            'answer'=>$answer['value'],
                            'correct_answer'=>$thisAnswer->correct_answer,
                            'extra_answer'=>$thisAnswer->extra_answer,
                            'is_correct'=>$isCorrect,
                        ];
                        if ($isCorrect){$totalCorrect++;}else{$wrongAnswers++;}
                    }
                }


                $validatedData['part_six']=$partSixValidatedData;

            }else{
                $validatedData['part_six']=[];
            }

        }



        return [$validatedData, $totalCorrect, $wrongAnswers, $totalQuestions];
    }

    public function accessMockResult($cefrExercise,$cefrResult,$user,$type)
    {
        if ($cefrExercise->full_exercise){
            $resultExists=FullResult::where('full_exercise_id',$cefrExercise->full_exercise->id)
                ->where('status',0)
                ->where('user_id',$user->id)
                ->orderByDesc('id')
                ->first();

            if ($resultExists){
                $data=[
                    'status'=>0,
                ];

                if ($type==CefrTypeEnum::LISTENING->value){
                    $data['listening_result_id']=$cefrResult->id;
                }elseif ($type==CefrTypeEnum::WRITING->value){
                    $data['writing_result_id']=$cefrResult->id;
                }elseif ($type==CefrTypeEnum::READING->value){
                    $data['reading_result_id']=$cefrResult->id;
                }elseif ($type==CefrTypeEnum::SPEAKING->value){
                    $data['speaking_result_id']=$cefrResult->id;
                }

                $resultExists = $this->editFullResult($resultExists,$data);
            }else{
                $data=[
                    'status'=>0,
                    'user_id'=>$user->id,
                ];

                if ($type==CefrTypeEnum::LISTENING->value){
                    $data['listening_result_id']=$cefrResult->id;
                }elseif ($type==CefrTypeEnum::WRITING->value){
                    $data['writing_result_id']=$cefrResult->id;
                }elseif ($type==CefrTypeEnum::READING->value){
                    $data['reading_result_id']=$cefrResult->id;
                }elseif ($type==CefrTypeEnum::SPEAKING->value){
                    $data['speaking_result_id']=$cefrResult->id;
                }

                $resultExists = $this->createFullResult($cefrExercise->full_exercise->id,$data);
            }

            $this->ifAccessFinishResult($resultExists);

            return ['success'=>true,'result'=>$resultExists,'id'=>$cefrExercise->full_exercise->id];

        }else{
            return ['success'=>false];
        }
    }

    public function ifAccessFinishResult($fullResult): void
    {
        if (!$fullResult) {
            return;
        }

        // FullResult -> full_exercise relation bo'lsa shuni ishlat
        // Aks holda sen yozganingdek "exercise" emas, "full_exercise" bo'lishi kerak.
        $fullResult->load([
            'exercise.listening_exercise',
            'exercise.reading_exercise',
            'exercise.writing_exercise',
            'exercise.speaking_exercise',
            'listening_result',
            'reading_result',
            'writing_result',
            'speaking_result',
        ]);

        $fullExercise = $fullResult->exercise;

        if (!$fullExercise) {
            return;
        }

        // Access tekshiruvlar
        $listeningAccess = $this->generateListenignAccess($fullExercise->listening_exercise);
        $readingAccess   = $this->generateReadingAccess($fullExercise->reading_exercise);
        $writingAccess   = $this->generateWritingAccess($fullExercise->writing_exercise);
        $speakingAccess  = $this->generateSpeakingAccess($fullExercise->speaking_exercise);

        $mustHaveResult = function (array $acc, $result): bool {
            $a = (int)($acc['access'] ?? 0);
            return $a !== 1 || (bool) $result; // access=1 => result required
        };

        $access =
            $mustHaveResult($listeningAccess, $fullResult->listening_result) &&
            $mustHaveResult($readingAccess,   $fullResult->reading_result) &&
            $mustHaveResult($writingAccess,   $fullResult->writing_result) &&
            $mustHaveResult($speakingAccess,  $fullResult->speaking_result);

        if ($access) {
            $this->editFullResult($fullResult, ['status' => 1,'date'=>now()]);
        }
    }


    public function generateListenignAccess(?\App\Models\ListeningExercise $listeningExercise): array
    {
        if (!$listeningExercise) {
            return [
                'access' => 0,
                'status' => 0,
            ];
        }

        $listeningExercise->load([
            'part_one.tests',
            'part_five.tests',
            'part_two.listening_part_two_six_tests',
            'part_six.listening_part_two_six_tests',
            'part_three.part_three_four_questions',
            'part_four.part_three_four_questions',
        ]);

        $hasAccess =
            $listeningExercise->part_one &&
            $listeningExercise->part_one->tests->count() >= 2 &&

            $listeningExercise->part_five &&
            $listeningExercise->part_five->tests->count() >= 2 &&

            $listeningExercise->part_two &&
            $listeningExercise->part_two->listening_part_two_six_tests->count() >= 2 &&

            $listeningExercise->part_six &&
            $listeningExercise->part_six->listening_part_two_six_tests->count() >= 2 &&

            $listeningExercise->part_three &&
            $listeningExercise->part_three->part_three_four_questions->count() >= 2 &&

            $listeningExercise->part_four &&
            $listeningExercise->part_four->part_three_four_questions->count() >= 2;

        return [
            'access' => $hasAccess ? 1 : 0,
            'status' => $hasAccess ? 1 : 0,
        ];
    }
    public function generateReadingAccess(?\App\Models\ReadingExercise $readingExercise): array
    {
        if (!$readingExercise) {
            return [
                'access' => 0,
                'status' => 0,
            ];
        }

        // N+1 bo‘lmasin deb nested relations bilan yuklaymiz
        $readingExercise->load([
            'part_one.answers',
            'part_two.texts',
            'part_three.texts',
            'part_four.tests',
            'part_five.tests',
            'part_five.answers',
        ]);

        $hasAccess =
            $readingExercise->part_one &&
            $readingExercise->part_one->answers->count() >= 2 &&

            $readingExercise->part_five &&
            $readingExercise->part_five->tests->count() >= 2 &&
            $readingExercise->part_five->answers->count() >= 2 &&

            $readingExercise->part_two &&
            $readingExercise->part_two->texts->count() >= 2 &&

            $readingExercise->part_three &&
            $readingExercise->part_three->texts->count() >= 2 &&

            $readingExercise->part_four &&
            $readingExercise->part_four->tests->count() >= 2;

        return [
            'access' => $hasAccess ? 1 : 0,
            'status' => $hasAccess ? 1 : 0,
        ];
    }

    public function generateWritingAccess(?\App\Models\WritingExercise $writingExercise): array
    {
        if (!$writingExercise) {
            return [
                'access' => 0,
                'status' => 0,
            ];
        }

        $writingExercise->load([
            'task_one',
            'task_two',
        ]);

        $hasAccess = (bool) ($writingExercise->task_one && $writingExercise->task_two);

        return [
            'access' => $hasAccess ? 1 : 0,
            'status' => $hasAccess ? 1 : 0,
        ];
    }
    public function generateSpeakingAccess(?\App\Models\SpeakingExercise $speakingExercise): array
    {
        if (!$speakingExercise) {
            return [
                'access' => 0,
                'status' => 0,
            ];
        }

        $speakingExercise->load([
            'part_one_first.questions',
            'part_one_second.questions',
            'part_one_second.images',
            'part_two.questions',
            'part_two.images',
            'part_three',
        ]);

        $hasAccess =
            $speakingExercise->part_one_first &&
            $speakingExercise->part_one_first->questions->count() >= 1 &&

            $speakingExercise->part_one_second &&
            $speakingExercise->part_one_second->questions->count() >= 1 &&
            $speakingExercise->part_one_second->images->count() >= 1 &&

            $speakingExercise->part_two &&
            $speakingExercise->part_two->questions->count() >= 1 &&
            $speakingExercise->part_two->images->count() >= 1 &&

            $speakingExercise->part_three &&
            filled($speakingExercise->part_three->question_text);

        return [
            'access' => $hasAccess ? 1 : 0,
            'status' => $hasAccess ? 1 : 0,
        ];
    }

    public function createFullResult($full_exercise_id,$data)
    {
        $data['full_exercise_id']=$full_exercise_id;

       return  FullResult::create($data);
    }

    public function editFullResult($fullResult,$data)
    {
        $fullResult->update($data);

        return $fullResult;
    }

    public function validateWritingText($text, $maxWords = 0, $maxLength = 0)
    {
        $text = trim($text);

        // 1. Character limit
        if ($maxLength > 0 && mb_strlen($text) > $maxLength) {
            $text = mb_substr($text, 0, $maxLength);
        }

        // 2. Word limit (newline saqlanadi)
        if ($maxWords > 0) {
            preg_match_all('/\S+|\n/', $text, $matches);

            $tokens = $matches[0];
            $wordCount = 0;
            $result = [];

            foreach ($tokens as $token) {
                if ($token !== "\n") {
                    $wordCount++;
                }

                if ($wordCount > $maxWords) {
                    break;
                }

                $result[] = $token;
            }

            $text = implode(' ', $result);

            // newline larni qayta tiklash
            $text = str_replace(" \n ", "\n", $text);
            $text = str_replace(" \n", "\n", $text);
            $text = str_replace("\n ", "\n", $text);
        }

        return $text;
    }
}
