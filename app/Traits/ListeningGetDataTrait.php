<?php

namespace App\Traits;

use App\Enums\CefrTypeEnum;
use App\Models\ListeningPartOneFifeQuestion;
use App\Models\ListeningPartOneFiveAnswers;
use App\Models\ListeningPartThreeFour;
use App\Models\ListeningPartTwoSixQuestion;
use App\Models\ListeningResult;
use App\Models\PrepareText;
use App\Models\ListeningExercise;
use App\Models\ReadingPartFour;
use App\Models\ReadingPartOneFiveQuestion;
use App\Models\ReadingPartTwoThree;
use App\Models\ReadingPartTwoThreeAnswer;
use App\Models\ReadingResult;
use Illuminate\Support\Facades\Storage;

trait ListeningGetDataTrait
{
    public function GetListeningData($id,$exam_type)
    {
        $listening_exercise = ListeningExercise::where('id', $id)->with(['part_one', 'part_two', 'part_three', 'part_four', 'part_five','full_exercise'])->first();

        if (!$listening_exercise){
            abort(404);
        }

        $part_category = '';
        if (isset(ListeningExercise::PART_CATEGORIES[$listening_exercise->part_category])) {
            $part_category = ListeningExercise::PART_CATEGORIES[$listening_exercise->part_category];
        }

        return [
            'id' => $listening_exercise->id,
            'mock_id'=>$listening_exercise->full_exercise? $listening_exercise->full_exercise->id : 0,
            'name' => $listening_exercise->name,
            'status' => $listening_exercise->status,
            'type' => CefrTypeEnum::LISTENING->value,
            'part_category' => $part_category,
            'is_mock' => $listening_exercise->full_exercise? 1 : 0,
            'part_category_number' => $listening_exercise->part_category,
            'difficulty' => $listening_exercise->difficulty,
            'asset_url' => asset('/'),
            'time_limit' => $listening_exercise->time_limit,
            'audio_control' => $exam_type,
            'part_one' => $this->GetListeningPartOneData($listening_exercise->part_one),
            'part_two' => $this->GetListeningPartTwoSixData($listening_exercise->part_two),
            'part_three' => $this->GetListeningPartThreeFourData($listening_exercise->part_three),
            'part_four' => $this->GetListeningPartThreeFourData($listening_exercise->part_four),
            'part_five' => $this->GetListeningPartFiveData($listening_exercise->part_five),
            'part_six' => $this->GetListeningPartTwoSixData($listening_exercise->part_six),
        ];
    }

    public function GetListeningPartOneData($part_one)
    {
        if ($part_one) {
            $partOne = ListeningPartOneFifeQuestion::where('id', $part_one->id)->with(['tests'])->first();

            $tests = [];

            foreach ($partOne->tests as $test) {
                $tests[] = [
                    'id' => $test->id,
                    'answer_1' => $test->answer_1,
                    'answer_2' => $test->answer_2,
                    'answer_3' => $test->answer_3,
                    //                    'correct_answer'=>$test->correct_answer,
                ];
            }

            return [
                'id' => $partOne->id,
                'advice' => $partOne->advice,
                'audio' => $this->getAudiourl($partOne->audio),
                'tests' => $tests,
            ];
        } else {
            return null;
        }
    }

    public function GetListeningPartTwoSixData($part_one)
    {
        if ($part_one) {
            $partTwo = ListeningPartTwoSixQuestion::where('id', $part_one->id)->with(['answers'])->first();

            $answerdata = [];

            foreach ($partTwo->answers as $answer) {
                $answerdata[] = [
                    'id' => $answer->id,
                    'count_number' => $answer->count_number,
                ];
            }

            return [
                'id' => $partTwo->id,
                'audio' => $this->getAudiourl($partTwo->audio),
                'advice' => $partTwo->advice,
                'title' => $partTwo->title ?? null,
                'text' => $partTwo->text,
                'answers' => $answerdata,
            ];
        } else {
            return null;
        }
    }
    public function GetListeningPartThreeFourData($part_three)
    {
        if ($part_three) {
            $partThreeFour = ListeningPartThreeFour::where('id', $part_three->id)->with(['questions', 'answers'])->first();

            $questionsData = [];
            $answerData = [];

            foreach ($partThreeFour->questions as $question) {
                $questionsData[] = [
                    'id' => $question->id,
                    'title' => $question->title,
                    //                    'correct_answer_id'=>$question->correct_answer_id,
                ];
            }

            foreach ($partThreeFour->answers as $answer) {
                $answerData[] = [
                    'id' => $answer->id,
                    'letter_key' => $answer->letter,
                    'answer' => $answer->answer,
                ];
            }

            return [
                'id' => $partThreeFour->id,
                'advice' => $partThreeFour->advice,
                'audio' => $this->getAudiourl($partThreeFour->audio),
                'map' => $this->getMapUrl($partThreeFour->image),
                'letter_array' => ReadingPartTwoThreeAnswer::LETTERS,
                'questions' => $questionsData,
                'answers' => $answerData,
            ];

        } else {
            return null;
        }
    }
    public function GetListeningPartFiveData($part_four)
    {
        if ($part_four) {
            $partFive = ListeningPartOneFifeQuestion::where('id', $part_four->id)->with(['tests'])->first();

            $tests = [];

            foreach ($partFive->tests as $test) {
                $tests[] = [
                    'id' => $test->id,
                    'question' => $test->question,
                    'answer_1' => $test->answer_1,
                    'answer_2' => $test->answer_2,
                    'answer_3' => $test->answer_3,
                    //                    'correct_answer'=>$test->correct_answer, // 1,2,3,4
                ];
            }

            return [
                'id' => $partFive->id,
                'advice' => $partFive->advice,
                'audio' => $this->getAudiourl($partFive->audio),
                'text' => $partFive->text,
                'tests' => $tests,
            ];

        } else {
            return null;
        }
    }

    public function GetListeningResultData($result_id)
    {

        $listeningResult = ListeningResult::with([
            'PartOneAnswers',
            'PartTwoAnswers',
            'PartThreeAnswers',
            'PartFourAnswers',
            'PartFiveAnswers',
            'PartSixAnswers',
        ])->findOrFail($result_id);

        $listening_exercise = ListeningExercise::where('id', $listeningResult->listening_exercise_id)
            ->with(['part_one', 'part_two', 'part_three.questions', 'part_four', 'part_five'])->first();

        $part_category = '';
        if (isset(ListeningExercise::PART_CATEGORIES[$listening_exercise->part_category])) {
            $part_category = ListeningExercise::PART_CATEGORIES[$listening_exercise->part_category];
        }
        $parts_data=$this->GetPartsData($listening_exercise);
        $active = (array_keys($parts_data)[0] ?? null);
        $createdText = \Illuminate\Support\Carbon::parse($listeningResult->created_at)
            ->locale('uz')
            ->translatedFormat('j F, H:i');

        $percentageData=$this->getListeningPersentageData($listeningResult);

        return [
            'id' => $listeningResult->id,
            'listening_exercise_id' => $listening_exercise->id,
            'createdText' => $createdText,
            'name' => $listening_exercise->name,
            'status' => $listening_exercise->status,
            'type' => CefrTypeEnum::LISTENING->value,
            'part_category' => $part_category,
            'part_category_number' => $listening_exercise->part_category,
            'difficulty' => $listening_exercise->difficulty,
            'asset_url' => asset('/'),
            'time_limit' => $listening_exercise->time_limit,
            'audio_control' => 0,
            'all_answers' => $listeningResult->all_answers,
            'correct_answers' => $listeningResult->correct_answers,
            'wrong_answers' => $listeningResult->wrong_answers,
            'feedback' => 'Your score is very low. You need to improve your basics.',
            'suggestion' => 'Focus on simple English listening and reading exercises daily',
            'percentageData' => $percentageData,
            'parts_data' => $parts_data,
            'active' => $active,
            'part_one' => $this->GetListeningPartOneFiveResultData($listening_exercise->part_one, $listeningResult->PartOneAnswers, 0),
            'part_two' => $this->GetListeningPartTwoSixResultData($listening_exercise->part_two, $listeningResult->PartTwoAnswers),
            'part_three' => $this->GetListeningPartThreeFourResultData($listening_exercise->part_three, $listeningResult->PartThreeAnswers, 0),
            'part_four' => $this->GetListeningPartThreeFourResultData($listening_exercise->part_four, $listeningResult->PartFourAnswers, 1),
            'part_five' => $this->GetListeningPartOneFiveResultData($listening_exercise->part_five, $listeningResult->PartFiveAnswers, 1),
            'part_six' => $this->GetListeningPartTwoSixResultData($listening_exercise->part_six, $listeningResult->PartSixAnswers),
        ];
    }

    public function GetListeningPartOneFiveResultData($part_one_five, $userAnswers, $type)
    {
        if (!$part_one_five)
            return null;

        $partOne = ListeningPartOneFifeQuestion::where('id', $part_one_five->id)
            ->with(['tests'])
            ->first();

        $userMap = collect($userAnswers)->keyBy('test_id');

        $tests = [];
        $question_data=[];

        foreach ($partOne->tests as $test) {
            $userRow = $userMap->get($test->id);

            $testData = [
                'id' => $test->id,
                'answer_1' => $test->answer_1,
                'answer_2' => $test->answer_2,
                'answer_3' => $test->answer_3,
                'correct_answer_key' => $this->getAnswerKeyPartOneFive($test->correct_answer),
            ];

            if ($type) {
                $testData['question'] = $test->question;
            }


            $test_data= $this->injectAnswerResultListening(
                $testData,
                $userRow,
                [
                    'user_answer_key' => 'answer_key',
                    'correct_answer' => 'correct_answer',
                    'is_correct' => 'is_correct',
                ]
            );
            $tests[] = $test_data;

            $question_data[] = [
                'user_answer'=>$test_data['status']=='answered' ?$test_data[$test_data['user_answer_key']]  : 'Not answered',
                'correct_answer'=>$test_data[$test_data['correct_answer_key']],
                'is_correct'=>$test_data['is_correct'] ? true : false,
            ];
        }


        return [
            'id' => $partOne->id,
            'advice' => $partOne->advice,
            'audio' => $this->getAudiourl($partOne->audio),
            'tests' => $tests,
            'question_data' => $question_data,
        ];

    }

    public function GetListeningPartTwoSixResultData($part_two_six, $userAnswers)
    {
        if (!$part_two_six)
            return null;

        $partTwoSix = ListeningPartTwoSixQuestion::where('id', $part_two_six->id)
            ->with(['answers'])
            ->first();

        $userMap = collect($userAnswers)->keyBy('answer_id');

        $answersData = [];
        $question_data=[];

        foreach ($partTwoSix->answers as $answer) {
            $userRow = $userMap->get($answer->id);

            $answerData= $this->injectAnswerResult([
                'id' => $answer->id,
                'count_number' => $answer->count_number,
                'extra_answer' => $answer->extra_answer,
                'correct_answer' => $answer->correct_answer,
            ], $userRow, [
                'user_answer' => 'answer',
                'is_correct' => 'is_correct',
            ]);

            $answersData[] = $answerData;

            $question_data[] = [
                'user_answer'=>$answerData['status']=='answered' ? $answerData['user_answer']  : 'Not answered',
                'correct_answer'=>$answerData['correct_answer'],
                'is_correct'=>$answerData['is_correct'] ? true : false,
            ];
        }
        return [
            'id' => $partTwoSix->id,
            'audio' => $this->getAudiourl($partTwoSix->audio),
            'advice' => $partTwoSix->advice,
            'title' => $partTwoSix->title ?? null,
            'text' => $partTwoSix->text,
            'answers' => $answersData,
            'question_data' => $question_data,
        ];

    }

    public function GetListeningPartThreeFourResultData($part_three_four, $userAnswers, $type)
    {
        if (!$part_three_four)
            return null;

        $partThreeFour = ListeningPartThreeFour::where('id', $part_three_four->id)
            ->with(['questions', 'answers'])
            ->first();

        $userAnswers = collect($userAnswers);

        $selectedAnswerIds = $userAnswers->pluck('answer_id')->filter()->unique()->values()->all();

        $questionsData = [];
        $letter_array=ReadingPartTwoThreeAnswer::LETTERS;

        foreach ($partThreeFour->questions as $question) {
            $questionsData[] = [
                'id' => $question->id,
                'title' => $question->title,
                'correct_answer_id' => $question->correct_answer_id,
            ];
        }

        $answerData = [];

        foreach ($partThreeFour->answers as $answer) {
            $answerData[] = [
                'id' => $answer->id,
                'letter_key' => $answer->letter,
                'answer' => $type ? ReadingPartTwoThreeAnswer::LETTERS[$answer->letter] : $answer->answer,
                'is_selected' => in_array($answer->id, $selectedAnswerIds, true),
            ];
        }

        $resultRows = $userAnswers->values()->map(function ($row) {
            return [
                'question_id' => $row->question_id ?? null,
                'answer_id' => $row->answer_id ?? null,
                'correct_answer_id' => $row->correct_answer_id ?? null,
                'correct_answer' => $row->correct_answer ?? null,
                'is_correct' => isset($row->is_correct) ? (int) $row->is_correct : 0,
                'status' => 'answered',
            ];
        })->all();

        $question_data = [];

        foreach ($partThreeFour->questions as $question) {

            if ($partThreeFour->type == '0'){
                $user_answer= $userAnswers->where('question_id', $question->id)->first();
                $user_answer_text =  $user_answer ? $partThreeFour->answers->where('id',$user_answer->answer_id)->first()->answer : null;
                $correct_answer_text =  $partThreeFour->answers->where('id',$question->correct_answer_id)->first()->answer;

                $question_data[] = [
                    'user_answer'=>$user_answer ? $user_answer_text : 'Not answered',
                    'correct_answer'=>$correct_answer_text ?? ' ',
                    'is_correct'=>$user_answer ? ($user_answer->is_correct ? true : false) : false,
                ];
            }elseif ($partThreeFour->type == '1'){
                $user_answer= $userAnswers->where('question_id', $question->id)->first();
                $user_answer_text =  $user_answer ? $letter_array[$partThreeFour->answers->where('id',$user_answer->answer_id)->first()->letter] : null;
                $correct_answer_text =  $letter_array[$partThreeFour->answers->where('id',$question->correct_answer_id)->first()->letter];

                $question_data[] = [
                    'user_answer'=>$user_answer ? $user_answer_text : 'Not answered',
                    'correct_answer'=>$correct_answer_text ?? ' ',
                    'is_correct'=>$user_answer ? ($user_answer->is_correct ? true : false) : false,
                ];

            }

        }


        return [
            'id' => $partThreeFour->id,
            'advice' => $partThreeFour->advice,
            'audio' => $this->getAudiourl($partThreeFour->audio),
            'map' => $this->getMapUrl($partThreeFour->image),
            'letter_array' => $letter_array,
            'questions' => $questionsData,
            'answers' => $answerData,
            'result_rows' => $resultRows,
            'question_data' => $question_data,
        ];

    }
    public function getAudiourl($audio)
    {
        if (app()->environment('local')) {
        }

        return Storage::url('public/audios/' . $audio);
    }

    public function getMapUrl($image)
    {
        if ($image) {
            return $image ? 'public/maps/' . $image : 'public/maps/default.png';
        } else {
            return '';
        }
    }
    public function getAnswerKeyPartOneFive($correct_answer)
    {

        return 'answer_' . $correct_answer;
    }
    private function injectAnswerResultListening(array $item, $userRow, array $mapFields): array
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
            $item['is_correct'] = isset($item['is_correct']) ? (int) $item['is_correct'] : null;
        }

        return $item;
    }

    public function getListeningPersentageData($listeningResult)
    {
        $all = (int) ($listeningResult->all_answers ?? 0);
        $correct = (int) ($listeningResult->correct_answers ?? 0);

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
        $is_mock=$listeningResult->exercise->part_category==7 ? true : false;

        if ($is_mock){
            $mock_ball=CefrTypeEnum::LISTENING_BALL[$correct];
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

    public function getPartsData($listening_exercise)
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

        if ($listening_exercise->part_six) {
            $parts['part_six'] = 'Part Six';
        }

        return $parts;
    }

}
