<?php

namespace App\Traits;

use App\Enums\CefrTypeEnum;
use App\Http\Resources\Reading\ReadingExerciseWithPartResource;
use App\Http\Resources\Reading\ReadingPartFiveResource;
use App\Http\Resources\Reading\ReadingPartFourResource;
use App\Http\Resources\Reading\ReadingPartOneAnswersResource;
use App\Http\Resources\Reading\ReadingPartOneResource;
use App\Http\Resources\Reading\ReadingPartTwoAnswersResource;
use App\Http\Resources\Reading\ReadingPartTwoResource;
use App\Http\Resources\Reading\ReadingPartTwotextsResource;
use App\Models\PrepareText;
use App\Models\ReadingExercise;
use App\Models\ReadingPartFour;
use App\Models\ReadingPartOneFiveQuestion;
use App\Models\ReadingPartOneFiveTest;
use App\Models\ReadingPartTwoThree;
use App\Models\ReadingPartTwoThreeAnswer;
use App\Models\ReadingResult;

trait ReadingGetDataTrait
{
    public function GetReadingData($id)
    {
        $reading_exercise=ReadingExercise::where('id',$id)->with(['part_one','part_two','part_three','part_four','part_five'])->first();

        $part_category='';
        if (isset(ReadingExercise::PART_CATEGORIES[$reading_exercise->part_category])){
            $part_category=ReadingExercise::PART_CATEGORIES[$reading_exercise->part_category];
        }

        $array = [1 => 20, 2 => 24, 3 => 27, 4 => 29, 5 => 32, 6 => 34, 7 => 36, 8 => 38, 9 => 39, 10 => 41, 11 => 42, 12 => 44, 13 => 45, 14 => 46, 15 => 48, 16 => 49, 17 => 51, 18 => 52, 19 => 54, 20 => 55, 21 => 57, 22 => 58, 23 => 60, 24 => 61, 25 => 63, 26 => 65, 27 => 66, 28 => 68, 29 => 70, 30 => 71, 31 => 73, 32 => 74, 33 => 75, 34 => 75, 35 => 75,];

        return [
            'id'=>$reading_exercise->id,
            'mock_id'=>$reading_exercise->full_exercise? $reading_exercise->full_exercise->id : 0,
            'name'=>$reading_exercise->name,
            'status'=>$reading_exercise->status,
            'type' => CefrTypeEnum::READING->value,
            'part_category'=>$part_category,
            'is_mock' => $reading_exercise->full_exercise? 1 : 0,
            'part_category_number'=>$reading_exercise->part_category,
            'difficulty'=>$reading_exercise->difficulty,
            'time_limit'=>$reading_exercise->time_limit,
            'result_array'=>$array,
            'part_one'=>$this->GetReadingPartOneData($reading_exercise->part_one ),
            'part_two'=>$this->GetReadingPartTwoThreeData($reading_exercise->part_two ),
            'part_three'=>$this->GetReadingPartTwoThreeData($reading_exercise->part_three ),
            'part_four'=>$this->GetReadingPartFourData($reading_exercise->part_four ),
            'part_five'=>$this->GetReadingPartFiveData($reading_exercise->part_five ),
        ];
    }

    public function GetReadingPartOneData($part_one)
    {
        if ($part_one){
            $partOne=ReadingPartOneFiveQuestion::where('id',$part_one->id)->with(['answers'])->first();

            $answerdata=[];

            foreach ($partOne->answers as $answer){
                $answerdata[]=[
                    'id'=>$answer->id,
                    'count_number'=>$answer->count_number,
//                    'correct_answer'=>$answer->correct_answer,
                    'extra_answer'=>$answer->extra_answer,
                ];
            }

            return [
                'id'=>$partOne->id,
                'advice'=>$partOne->advice,
                'title'=>$partOne->title ?? null,
                'text'=>$partOne->question_text,
                'answers'=> $answerdata,
            ];
        }else{
            return null;
        }
    }

    public function GetReadingPartTwoThreeData($part_two)
    {
        if ($part_two){
            $partTwo=ReadingPartTwoThree::where('id',$part_two->id)->with(['texts','answers'])->first();

            $textsData=[];
            $answerData=[];

            foreach ($partTwo->texts as $text){
                $textsData[]=[
                    'id'=>$text->id,
                    'text'=>$text->text,
//                    'correct_answer_id'=>$text->correct_answer_id,
                ];
            }

            foreach ($partTwo->answers as $answer){
                $answerData[]=[
                    'id'=>$answer->id,
                    'letter_key'=>$answer->letter,
                    'answer'=>$answer->answer,
                ];
            }

            return [
                'id'=>$partTwo->id,
                'advice'=>$partTwo->advice,
                'letter_array'=>ReadingPartTwoThreeAnswer::LETTERS,
                'texts'=>$textsData,
                'answers'=>$answerData,
            ];

        }else{
            return null;
        }
    }
    public function GetReadingPartFourData($part_four)
    {
        if ($part_four){
            $partFour=ReadingPartFour::where('id',$part_four->id)->with(['tfn_tests','mchq_tests'])->first();

            $TFNg_tests=[];
            $MCHQ_tests=[];

            foreach ($partFour->tfn_tests as $test){
                $TFNg_tests[]=[
                    'id'=>$test->id,
                    'question'=>$test->question,
                    'answer_1'=>$test->answer_1,
                    'answer_2'=>$test->answer_2,
                    'answer_3'=>$test->answer_3,
//                    'correct_answer'=>$test->correct_answer, // 1,2,3,4
                ];
            }

            foreach ($partFour->mchq_tests as $mchq_test){
                $MCHQ_tests[]=[
                    'id'=>$mchq_test->id,
                    'question'=>$mchq_test->question,
                    'answer_1'=>$mchq_test->answer_1,
                    'answer_2'=>$mchq_test->answer_2,
                    'answer_3'=>$mchq_test->answer_3,
                    'answer_4'=>$mchq_test->answer_4 ?? '',
//                    'correct_answer'=>$mchq_test->correct_answer, // 1,2,3,4
                ];
            }

            return [
                'id'=>$partFour->id,
                'advice'=>$partFour->advice,
                'text'=>$partFour->text,
                'TFNg_tests'=>$TFNg_tests,
                'MCHQ_tests'=>$MCHQ_tests,
            ];

        }else{
            return null;
        }
    }

    public function GetReadingPartFiveData($part_five)
    {
        if ($part_five){
            $partFive=ReadingPartOneFiveQuestion::where('id',$part_five->id)->with(['answers','tests'])->first();

            $answerdata=[];
            $testsdata=[];

            foreach ($partFive->answers as $answer){
                $answerdata[]=[
                    'id'=>$answer->id,
                    'count_number'=>$answer->count_number,
//                    'correct_answer'=>$answer->correct_answer,
                    'extra_answer'=>$answer->extra_answer,
                ];
            }

            foreach ($partFive->tests as $test){
                $testsdata[]=[
                    'id'=>$test->id,
                    'question'=>$test->question,
                    'answer_1'=>$test->answer_1,
                    'answer_2'=>$test->answer_2,
                    'answer_3'=>$test->answer_3,
                    'answer_4'=>$test->answer_4,
                    'correct_answer'=>$test->correct_answer, // 1,2,3,4
                ];
            }

            return [
                'id'=>$partFive->id,
                'advice'=>$partFive->advice,
                'reading_text'=>$partFive->reading_text,
                'question_text'=>$partFive->question_text,
                'answers'=> $answerdata,
                'tests'=> $testsdata,
            ];
        }else{
            return null;
        }
    }

// =========================
// RESULT HELPERS
// =========================

    private function injectAnswerResult(array $item, $userRow, array $mapFields): array
    {
        if (!$userRow) {
            $item['status'] = 'not_answered';
            foreach (array_keys($mapFields) as $outField) {
                $item[$outField] = null;
            }
            return $item;
        }

        $item['status'] = 'answered';

        foreach ($mapFields as $outField => $userField) {
            $item[$outField] = $userRow->{$userField} ?? null;
        }

        if (array_key_exists('is_correct', $mapFields)) {
            $item['is_correct'] = isset($item['is_correct']) ? (int)$item['is_correct'] : null;
        }

        return $item;
    }

    private function resultArrayMap(): array
    {
        return [1 => 20, 2 => 24, 3 => 27, 4 => 29, 5 => 32, 6 => 34, 7 => 36, 8 => 38, 9 => 39, 10 => 41, 11 => 42, 12 => 44, 13 => 45, 14 => 46, 15 => 48, 16 => 49, 17 => 51, 18 => 52, 19 => 54, 20 => 55, 21 => 57, 22 => 58, 23 => 60, 24 => 61, 25 => 63, 26 => 65, 27 => 66, 28 => 68, 29 => 70, 30 => 71, 31 => 73, 32 => 74, 33 => 75, 34 => 75, 35 => 75,];
    }
    public function GetReadingResultData($result_id)
    {
        $readingResult = ReadingResult::with([
            'PartOneAnswers',
            'PartTwoAnswers',
            'PartThreeAnswers',
            'PartFourAnswers',
            'PartFiveMCHQAnswers',
            'PartFiveFTHGAnswers',
        ])->findOrFail($result_id);

        $reading_exercise = ReadingExercise::where('id', $readingResult->reading_exercise_id)
            ->with(['part_one','part_two','part_three','part_four','part_five','exercise'])
            ->firstOrFail();

        $parts_data=$this->GetReadingPartsData($reading_exercise);
        $active = (array_keys($parts_data)[0] ?? null);
        $createdText = \Illuminate\Support\Carbon::parse($readingResult->created_at)
            ->locale('uz')
            ->translatedFormat('j F, H:i');

        $part_category = '';
        if (isset(ReadingExercise::PART_CATEGORIES[$reading_exercise->part_category])) {
            $part_category = ReadingExercise::PART_CATEGORIES[$reading_exercise->part_category];
        }

        $percentageData=$this->getReadingPersentageData($readingResult);

        return [
            'id' => $readingResult->id,
            'reading_exercise_id' => $reading_exercise->id,
            'name' => $reading_exercise->name,
            'status' => $reading_exercise->status,
            'active' => $active,
            'createdText' => $createdText,
            'type' => CefrTypeEnum::READING->value,
            'percentageData' => $percentageData,
            'part_category' => $part_category,
            'part_category_number' => $reading_exercise->part_category,
            'difficulty' => $reading_exercise->difficulty,
            'time_limit' => 60,
            'all_answers' => $readingResult->all_answers,
            'correct_answers' => $readingResult->correct_answers,
            'wrong_answers' => $readingResult->wrong_answers,
            'feedback' => 'Your score is very low. You need to improve your basics.',
            'suggestion' => 'Focus on simple English listening and reading exercises daily',
            'result' => [
                'reading_result_id' => $readingResult->id,
                'all_answers' => $readingResult->all_answers ?? null,
                'correct_answers' => $readingResult->correct_answers ?? null,
                'percent' => $readingResult->percent ?? null,
                'created_at' => $readingResult->created_at,
            ],
            'parts_data' => $parts_data,
            'part_one' => $this->GetReadingPartOneResultData($reading_exercise->part_one, $readingResult->PartOneAnswers),
            'part_two' => $this->GetReadingPartTwoThreeResultData($reading_exercise->part_two, $readingResult->PartTwoAnswers),
            'part_three' => $this->GetReadingPartTwoThreeResultData($reading_exercise->part_three, $readingResult->PartThreeAnswers),
            'part_four' => $this->GetReadingPartFourResultData($reading_exercise->part_four, $readingResult->PartFourAnswers),
            'part_five' => $this->GetReadingPartFiveResultData($reading_exercise->part_five, $readingResult->PartFiveFTHGAnswers, $readingResult->PartFiveMCHQAnswers),
        ];
    }

    public function GetReadingPartOneResultData($part_one, $userAnswers)
    {
        if (!$part_one) return null;

        $partOne = ReadingPartOneFiveQuestion::where('id', $part_one->id)
            ->with(['answers'])
            ->first();

        $userMap = collect($userAnswers)->keyBy('answer_id');

        $answersData = [];
        $question_data=[];

        foreach ($partOne->answers as $answer) {
            $userRow = $userMap->get($answer->id);

            $answerData = $this->injectAnswerResult([
                'id' => $answer->id,
                'count_number' => $answer->count_number,
                'extra_answer' => $answer->extra_answer,
                'correct_answer' => $answer->correct_answer,
            ], $userRow, [
                'user_answer'    => 'answer',
                'is_correct'     => 'is_correct',
            ]);

            $answersData[] = $answerData;

            $question_data[] = [
                'user_answer'=>$answerData['status']=='answered' ? $answerData['user_answer']  : 'Not answered',
                'correct_answer'=>$answerData['correct_answer'],
                'is_correct'=>$answerData['is_correct'] ? true : false,
            ];

        }

        return [
            'id' => $partOne->id,
            'advice' => $partOne->advice,
            'title' => $partOne->title ?? null,
            'text' => $partOne->question_text,
            'answers' => $answersData,
            'question_data' => $question_data,
        ];
    }

    public function GetReadingPartTwoThreeResultData($part_two, $userAnswers)
    {
        if (!$part_two) return null;

        $partTwo = ReadingPartTwoThree::where('id', $part_two->id)
            ->with(['texts','answers'])
            ->first();

        $userAnswers = collect($userAnswers);

        // user tanlagan answer_id lar
        $selectedAnswerIds = $userAnswers->pluck('answer_id')->filter()->unique()->values()->all();

        $question_data=[];
        $textsData = [];
        foreach ($partTwo->texts as $text) {
            $textsData[] = [
                'id' => $text->id,
                'text' => $text->text,
                'correct_answer_id'=>$text->correct_answer_id,
            ];
        }

        $answerData = [];
        foreach ($partTwo->answers as $answer) {
            $answerData[] = [
                'id' => $answer->id,
                'letter_key' => $answer->letter,
                'answer' => $answer->answer,

                // ✅ yangi
                'is_selected' => in_array($answer->id, $selectedAnswerIds, true),
            ];
        }

        // raw result rows (frontend uchun)
        $resultRows = $userAnswers->values()->map(function ($row) {
            return [
                'text_id' => $row->text_id ?? null,
                'answer_id' => $row->answer_id ?? null,
                'correct_answer_id' => $row->correct_answer_id ?? null,
                'correct_answer' => $row->correct_answer ?? null,
                'is_correct' => isset($row->is_correct) ? (int)$row->is_correct : null,
                'status' => 'answered',
            ];
        })->all();

        foreach ($partTwo->texts as $text) {
            $user_answer= $userAnswers->where('text_id', $text->id)->first();
            $user_answer_text =  $user_answer ? $partTwo->answers->where('id',$user_answer->answer_id)->first()->answer : null;
            $correct_answer_text =  $partTwo->answers->where('id',$text->correct_answer_id)->first()->answer;

            $question_data[] = [
                'user_answer'=>$user_answer ? $user_answer_text : 'Not answered',
                'correct_answer'=>$correct_answer_text ?? ' ',
                'is_correct'=>$user_answer ? ($user_answer->is_correct ? true : false) : false,
            ];
        }

        return [
            'id' => $partTwo->id,
            'advice' => $partTwo->advice,
            'letter_array' => ReadingPartTwoThreeAnswer::LETTERS,
            'texts' => $textsData,
            'answers' => $answerData,
            'question_data' => $question_data,
            'result_rows' => $resultRows,
        ];
    }

    public function GetReadingPartFourResultData($part_four, $userAnswers)
    {
        if (!$part_four) return null;

        $partFour = ReadingPartFour::where('id', $part_four->id)
            ->with(['tfn_tests','mchq_tests'])
            ->first();

        $userMap = collect($userAnswers)->keyBy('test_id');

        $question_data = [];
        $TFNg_tests = [];

        foreach ($partFour->tfn_tests as $test) {
            $userRow = $userMap->get($test->id);

            $test_data = $this->injectAnswerResult([
                'id' => $test->id,
                'question' => $test->question,
                'answer_1' => $test->answer_1,
                'answer_2' => $test->answer_2,
                'answer_3' => $test->answer_3,
                'correct_answer_key' => $this->getAnswerKeyPartFourFive($test->correct_answer),
            ], $userRow, [
                'user_answer_key' => 'answer_key',
                'correct_answer' => 'correct_answer',
                'is_correct' => 'is_correct',
            ]);

            $TFNg_tests[] = $test_data;

            $question_data[] = [
                'user_answer'=>$test_data['status']=='answered' ?$test_data[$test_data['user_answer_key']]  : 'Not answered',
                'correct_answer'=>$test_data[$test_data['correct_answer_key']],
                'is_correct'=>$test_data['is_correct'] ? true : false,
            ];
        }

        $MCHQ_tests = [];
        foreach ($partFour->mchq_tests as $mchq_test) {
            $userRow = $userMap->get($mchq_test->id);

            $test_data = $this->injectAnswerResult([
                'id' => $mchq_test->id,
                'question' => $mchq_test->question,
                'answer_1' => $mchq_test->answer_1,
                'answer_2' => $mchq_test->answer_2,
                'answer_3' => $mchq_test->answer_3,
                'answer_4' => $mchq_test->answer_4 ?? '',
                'correct_answer_key' => $this->getAnswerKeyPartFourFive($mchq_test->correct_answer),
            ], $userRow, [
                'user_answer_key' => 'answer_key',
//                'correct_answer_key' => 'correct_answer_key',
                'correct_answer' => 'correct_answer',
                'is_correct' => 'is_correct',
            ]);

            $MCHQ_tests[]=$test_data;

            $question_data[] = [
                'user_answer'=>$test_data['status']=='answered' ?$test_data[$test_data['user_answer_key']]  : 'Not answered',
                'correct_answer'=>$test_data[$test_data['correct_answer_key']],
                'is_correct'=>$test_data['is_correct'] ? true : false,
            ];
        }

        return [
            'id' => $partFour->id,
            'advice' => $partFour->advice,
            'text' => $partFour->text,
            'TFNg_tests' => $TFNg_tests,
            'MCHQ_tests' => $MCHQ_tests,
            'question_data' => $question_data,
        ];
    }

    public function GetReadingPartFiveResultData($part_five, $fthgUserAnswers, $mchqUserAnswers)
    {
        if (!$part_five) return null;

        $partFive = ReadingPartOneFiveQuestion::where('id', $part_five->id)->where('type',1)
            ->with(['answers','tests'])
            ->first();

        $fthgMap = collect($fthgUserAnswers)->keyBy('answer_id');
        $mchqMap = collect($mchqUserAnswers)->keyBy('test_id');

        $answersData = [];
        $question_data=[];

        foreach ($partFive->answers as $answer) {
            $userRow = $fthgMap->get($answer->id);

            $answerData = $this->injectAnswerResult([
                'id' => $answer->id,
                'count_number' => $answer->count_number,
                'extra_answer' => $answer->extra_answer,
                'correct_answer' => $answer->correct_answer,
            ], $userRow, [
                'user_answer'    => 'answer',
                'is_correct'     => 'is_correct',
            ]);

            $answersData[] = $answerData;

            $question_data[] = [
                'user_answer'=>$answerData['status']=='answered' ? $answerData['user_answer']  : 'Not answered',
                'correct_answer'=>$answerData['correct_answer'],
                'is_correct'=>$answerData['is_correct'] ? true : false,
            ];
        }

        $testsdata = [];
        foreach ($partFive->tests as $test) {
            $userRow = $mchqMap->get($test->id);

            $test_data = $this->injectAnswerResult([
                'id' => $test->id,
                'question' => $test->question,
                'answer_1' => $test->answer_1,
                'answer_2' => $test->answer_2,
                'answer_3' => $test->answer_3,
                'answer_4' => $test->answer_4,

                'correct_answer_key' => $this->getAnswerKeyPartFourFive($test->correct_answer),
                // siz oldin ham qaytarayotgan edingiz
                'correct_answer' => $test->correct_answer,
            ], $userRow, [
                'user_answer_key' => 'answer_key',
                'correct_answer' => 'correct_answer',
                'is_correct' => 'is_correct',
            ]);

            $testsdata[]=$test_data;

            $question_data[] = [
                'user_answer'=>$test_data['status']=='answered' ?$test_data[$test_data['user_answer_key']]  : 'Not answered',
                'correct_answer'=>$test_data[$test_data['correct_answer_key']],
                'is_correct'=>$test_data['is_correct'] ? true : false,
            ];
        }


        return [
            'id' => $partFive->id,
            'advice' => $partFive->advice,
            'reading_text' => $partFive->reading_text,
            'question_text' => $partFive->question_text,
            'answers' => $answersData,
            'tests' => $testsdata,
            'question_data' => $question_data,
        ];
    }
    public function getAnswerKeyPartFourFive($correct_answer)
    {

        return 'answer_'.$correct_answer;
    }

    public function getReadingPersentageData($readingResult)
    {
        $all = (int) ($readingResult->all_answers ?? 0);
        $correct = (int) ($readingResult->correct_answers ?? 0);

        // edge case: 0 bo‘lsa
        if ($all <= 0) {
            return [
                'percent' => 0,
                'array'   => 289,
                'offset'  => 289,
            ];
        }

        $percent = round(($correct / $all) * 100);

        $radius = 46;
        $circumference = round(2 * pi() * $radius); // ≈ 289

        $offset = round(
            $circumference - ($circumference * $percent / 100)
        );

        $mock_ball=null;
        $is_mock=( $readingResult->exercise->part_category == 6 ) ? true : false;

        if ($is_mock){
            $mock_ball=CefrTypeEnum::READING_BALL[$correct];
        }
        return [
            'percent' => $percent,        // masalan: 72
            'array'   => $circumference,  // 289
            'offset'  => $offset,         // masalan: 81
            'mock_ball'=>$mock_ball,
            'degree'=>$this->generateDegree($mock_ball),
            'is_mock'=>$is_mock,
        ];
    }
    public function generateDegree($ball)
    {
        if ($ball < 0 || $ball > 75) {
            return 'Invalid score';
        }

        if ($ball <= 37) {
            return 'Below B1';
        } elseif ($ball <= 50) {
            return 'B1';
        } elseif ($ball <= 64) {
            return 'B2';
        } else { // 65–75
            return 'C1';
        }
    }


    public function GetReadingPartsData($listening_exercise)
    {
        $parts = [];

        if ($listening_exercise->part_one) {
            $parts['part_one'] = 'Part One';
        }

        if ($listening_exercise->part_two) {
            $parts['part_two'] = 'Part Two';
        }

        if ($listening_exercise->part_three) {
            $parts['part_three'] = 'Part Three';
        }

        if ($listening_exercise->part_four) {
            $parts['part_four'] = 'Part Four';
        }

        if ($listening_exercise->part_five) {
            $parts['part_five'] = 'Part Five';
        }


        return $parts;
    }

}
