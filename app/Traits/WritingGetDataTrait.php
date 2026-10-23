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
use App\Models\WritingExercise;
use App\Models\WritingResult;
use Illuminate\Support\Facades\Storage;

trait WritingGetDataTrait
{
    public function GetWritingData($id)
    {
        $writingExercise = WritingExercise::with(['task_one','task_two','full_exercise'])->where('id',$id)->first();


        return [
            'writingExercise'=>$writingExercise,
            'mock_id'=>$writingExercise->full_exercise? $writingExercise->full_exercise->id : 0,
            'is_mock' => $writingExercise->full_exercise? 1 : 0,
            'part_category_number'=>$writingExercise->part_category,
            'type'=>CefrTypeEnum::WRITING->value,
            'task_one'=>$writingExercise->task_one,
            'task_two'=>$writingExercise->task_two,
            'task_one_time_limit'=>$writingExercise->task_one?->time_limit,
            'task_two_time_limit'=>$writingExercise->task_two?->time_limit,
        ];
    }

    public function GetWritingResultData($id)
    {
        $writingResult = WritingResult::with(['partOneAnswer','partTwoAnswer','exercise.task_one','exercise.task_two'])->find($id);
        $writingExercise=$writingResult->exercise;
        $percentageData=$this->getWritingPersentageData($writingResult);


        $createdText = \Illuminate\Support\Carbon::parse($writingResult->created_at)
            ->locale('uz')
            ->translatedFormat('j F, H:i');
        $tabs=$this->getTasksData($writingExercise);
        $status=$writingResult->status;

        return [
            'writingResult'=>$writingResult,
            'type'=>CefrTypeEnum::WRITING->value,
            'status'=>$status,
            'tabs'=>$tabs,
            'active'=>array_keys($tabs)[0],
            'task_one'=>$this->generateTaskOneData($writingExercise->task_one,$writingResult->partOneAnswer,$status),
            'task_two'=>$this->generateTaskTwoData($writingExercise->task_two,$writingResult->partTwoAnswer,$status),
            'percentageData' => $percentageData,
            'createdText'=>$createdText,
        ];
    }
    public function GetWritingResultApiData($id)
    {
        $writingResult = WritingResult::with(['partOneAnswer','partTwoAnswer','exercise.task_one','exercise.task_two'])->find($id);
        $writingExercise=$writingResult->exercise;
        $percentageData=$this->getWritingPersentageData($writingResult);


        $createdText = \Illuminate\Support\Carbon::parse($writingResult->created_at)
            ->locale('uz')
            ->translatedFormat('j F, H:i');
        $tabs=$this->getTasksData($writingExercise);
        $status=$writingResult->status;

        return [
            'writing_result_id'=>$writingResult->id,
            'name'=>$writingExercise->name ?? '',
            'category_type'=>$writingResult->category_type,
            'type'=>CefrTypeEnum::WRITING->value,
            'download_link' => $status==3 ? ( route('api.user.results.DownloadResultPdf', ['id'=>$writingResult->id, 'type'=>CefrTypeEnum::WRITING->value,'token'=>'wgp8kbrymkuaZEwNcAaMfQbTHFQmzKB6W57OecGW']) ): null,
            'status'=>$status,
            'tabs'=>$tabs,
            'active'=>array_keys($tabs)[0],
            'task_one'=>$this->generateTaskOneApiData($writingExercise->task_one,$writingResult->partOneAnswer,$status),
            'task_two'=>$this->generateTaskTwoApiData($writingExercise->task_two,$writingResult->partTwoAnswer,$status),
            'percentageData' => $percentageData,
            'createdText'=>$createdText,
        ];
    }

    public function generateTaskOneData($task_one,$task_one_result,$status)
    {
        if ($task_one && $task_one_result && ($status==3)) {
        $result_1_json=json_decode($task_one_result->result_1_json,true);
        $result_2_json=json_decode($task_one_result->result_2_json,true);
        if (isset($result_1_json['reGeneratedText']) ){
            $text_1=$this->reGenerateTextToHTML($result_1_json['reGeneratedText']);
        }else{
            $text_1=$this->reGenerateTextToHTML($task_one_result->text_1);
        }
        if (isset($result_2_json['reGeneratedText']) ){
            $text_2=$this->reGenerateTextToHTML($result_2_json['reGeneratedText']);
        }else{
            $text_2=$task_one_result->text_2;
        }
        $task_response=$this->mergeTaskResponses($result_1_json['task_response']??null,$result_2_json['task_response']??null);
        $description_1=isset($result_1_json['description']) ? $result_1_json['description'] : '';
        $description_2=isset($result_2_json['description']) ? $result_2_json['description'] : '';

            $resultData=[
                'score_1'=>rtrim(rtrim(number_format($task_one_result->score_1, 1, '.', ''), '0'), '.') ,
                'score_2'=>rtrim(rtrim(number_format($task_one_result->score_2, 1, '.', ''), '0'), '.') ,
                'max_score_1'=>CefrTypeEnum::WRITING_ADDITIONAL_MAX_SCORES[1],
                'max_score_2'=>CefrTypeEnum::WRITING_ADDITIONAL_MAX_SCORES[2],
                'ball'=>CefrTypeEnum::WRITING_BALL[1][rtrim(rtrim(number_format(floatval($task_one_result->score_1)+floatval($task_one_result->score_2), 1, '.', ''), '0'), '.')] ,
                'max_ball'=>CefrTypeEnum::WRITING_MAX_SCORES[1],
            ];

        return [
            'text_1'=>$text_1,
            'text_2'=>$text_2,
            'task_response'=>$task_response,
            'description_1'=>$description_1,
            'description_2'=>$description_2,
            'question'=>$task_one->theme,
            'resultData'=>$resultData,
        ];
        }else{
            if ($task_one){

            $text_1='';
            $text_2='';

            if ($task_one_result) {
                $text_1 =$this->reGenerateTextToHTML($task_one_result->text_1) ;
                $text_2 =$this->reGenerateTextToHTML($task_one_result->text_2) ;
            }

            return [
                'text_1'=>$text_1,
                'text_2'=>$text_2,
                'task_response'=>$this->getEmptyTaskResponse(),
                'description_1'=>'',
                'description_2'=>'',
                'question'=>$task_one->theme,
                'resultData'=>null,
            ];

            }else{
                return null;
            }

        }

    }
    public function generateTaskTwoData($task_two,$task_two_result,$status)
    {
        if ($task_two && $task_two_result && ($status==3)) {

        $result_json=json_decode($task_two_result->result_json,true);
        $text=isset($result_json['reGeneratedText']) ? $this->reGenerateTextToHTML($result_json['reGeneratedText']) : $task_two_result->text ;
        $task_response=isset($result_json['task_response'])? $result_json['task_response'] : $this->getEmptyTaskResponse() ;
        $description_1=isset($result_json['description']) ? $result_json['description'] : '';

        $resultData=[
            'score'=>rtrim(rtrim(number_format($task_two_result->score, 1, '.', ''), '0'), '.') ,
            'max_score'=>CefrTypeEnum::WRITING_ADDITIONAL_MAX_SCORES[3],
            'ball'=>CefrTypeEnum::WRITING_BALL[2][rtrim(rtrim(number_format($task_two_result->score, 1, '.', ''), '0'), '.')] ,
            'max_ball'=>CefrTypeEnum::WRITING_MAX_SCORES[2]
        ];

        return [
            'text'=>$text,
            'task_response'=>$task_response,
            'description'=>$description_1,
            'question'=>$task_two->theme,
            'resultData'=>$resultData,
        ];

        }else{
            if ($task_two){
                $text='';

                if ($task_two_result) {
                    $text = $task_two_result->text;
                }

                return [
                    'text'=>$text,
                    'task_response'=>$this->getEmptyTaskResponse(),
                    'description'=>'',
                    'question'=>$task_two->theme,
                    'resultData'=>null,
                ];
            }else{
                return null;

            }
        }

    }
    public function generateTaskOneApiData($task_one,$task_one_result,$status)
    {
        if ($task_one && $task_one_result && ($status==3)) {
        $result_1_json=json_decode($task_one_result->result_1_json,true);
        $result_2_json=json_decode($task_one_result->result_2_json,true);
        if (isset($result_1_json['reGeneratedText']) ){
            $text_1=$result_1_json['reGeneratedText'];
        }else{
            $text_1=$task_one_result->text_1;
        }
        if (isset($result_2_json['reGeneratedText']) ){
            $text_2=$result_2_json['reGeneratedText'];
        }else{
            $text_2=$task_one_result->text_2;
        }
        $task_response=$this->mergeTaskResponses($result_1_json['task_response']??null,$result_2_json['task_response']??null);
        $description_1=isset($result_1_json['description']) ? $result_1_json['description'] : '';
        $description_2=isset($result_2_json['description']) ? $result_2_json['description'] : '';

            $resultData=[
                'score_1'=>rtrim(rtrim(number_format($task_one_result->score_1, 1, '.', ''), '0'), '.') ,
                'score_2'=>rtrim(rtrim(number_format($task_one_result->score_2, 1, '.', ''), '0'), '.') ,
                'max_score_1'=>CefrTypeEnum::WRITING_ADDITIONAL_MAX_SCORES[1],
                'max_score_2'=>CefrTypeEnum::WRITING_ADDITIONAL_MAX_SCORES[2],
                'ball'=>CefrTypeEnum::WRITING_BALL[1][rtrim(rtrim(number_format(floatval($task_one_result->score_1)+floatval($task_one_result->score_2), 1, '.', ''), '0'), '.')] ,
                'max_ball'=>CefrTypeEnum::WRITING_MAX_SCORES[1],
            ];

        return [
            'text_1'=>$text_1,
            'text_2'=>$text_2,
            'task_response'=>$task_response,
            'description_1'=>$description_1,
            'description_2'=>$description_2,
            'question'=>$task_one->theme,
            'resultData'=>$resultData,
        ];
        }else{
            if ($task_one){

            $text_1='';
            $text_2='';

            if ($task_one_result) {
                $text_1 =$task_one_result->text_1 ;
                $text_2 =$task_one_result->text_2 ;
            }

            return [
                'text_1'=>$text_1,
                'text_2'=>$text_2,
                'task_response'=>$this->getEmptyTaskResponse(),
                'description_1'=>'',
                'description_2'=>'',
                'question'=>$task_one->theme,
                'resultData'=>null,
            ];

            }else{
                return null;
            }

        }

    }
    public function generateTaskTwoApiData($task_two,$task_two_result,$status)
    {
        if ($task_two && $task_two_result && ($status==3)) {

        $result_json=json_decode($task_two_result->result_json,true);
        $text=isset($result_json['reGeneratedText']) ? $result_json['reGeneratedText'] : $task_two_result->text ;
        $task_response=isset($result_json['task_response'])? $result_json['task_response'] : $this->getEmptyTaskResponse() ;
        $description_1=isset($result_json['description']) ? $result_json['description'] : '';

        $resultData=[
            'score'=>rtrim(rtrim(number_format($task_two_result->score, 1, '.', ''), '0'), '.') ,
            'max_score'=>CefrTypeEnum::WRITING_ADDITIONAL_MAX_SCORES[3],
            'ball'=>CefrTypeEnum::WRITING_BALL[2][rtrim(rtrim(number_format($task_two_result->score, 1, '.', ''), '0'), '.')] ,
            'max_ball'=>CefrTypeEnum::WRITING_MAX_SCORES[2]
        ];

        return [
            'text'=>$text,
            'task_response'=>$task_response,
            'description'=>$description_1,
            'question'=>$task_two->theme,
            'resultData'=>$resultData,
        ];

        }else{
            if ($task_two){
                $text='';

                if ($task_two_result) {
                    $text = $task_two_result->text;
                }

                return [
                    'text'=>$text,
                    'task_response'=>$this->getEmptyTaskResponse(),
                    'description'=>'',
                    'question'=>$task_two->theme,
                    'resultData'=>null,
                ];
            }else{
                return null;

            }
        }

    }

    public function reGenerateTextToHTML($text)
    {
        if (!$text) {
            return '';
        }

        // ── 0) Agar bu allaqachon generatsiya qilingan HTML bo'lsa (legacy data),
        //       qayta ishlamaymiz — faqat buzuq quote entity'larni tuzatamiz.
        if (str_contains($text, '<span class="text-')) {
            return $this->fixQuoteEntities($text);
        }

        // ── 1) Kirish matnidagi entity'larni decode qilamiz (double-encoding oldini oladi)
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // ── 2) Smart quote'larni ASCII ga normalize qilamiz (’ ‘ “ ” ` ´)
        $text = str_replace(
            ["\u{2018}", "\u{2019}", "\u{201C}", "\u{201D}", "\u{2032}", "\u{00B4}", "\u{0060}"],
            ["'",        "'",        '"',        '"',        "'",        "'",        "'"],
            $text
        );

        // ── 3) Butun matnni BIR MARTA escape qilamiz.
        //       ENT_NOQUOTES -> ' va " tegilmaydi (matn kontentida xavfsiz)
        //       double_encode = false
        //       { } [ ] ga htmlspecialchars ta'sir qilmaydi -> token'lar saqlanadi
        $text = htmlspecialchars($text, ENT_NOQUOTES, 'UTF-8', false);

        // ── 4) Bo'sh token'lar
        $text = preg_replace('/\{\s*\}|\[\s*\]/u', '', $text);

        // ── 5) Token -> span
        $text = preg_replace_callback('/\{([^}]*)\}|\[([^\]]*)\]/u', function ($m) {
            $isCorrect = ($m[0][0] === '{');
            $val = trim($isCorrect ? $m[1] : ($m[2] ?? ''));

            if ($val === '') {
                return '';
            }

            return $isCorrect
                ? '<span class="text-green-600 underline font-medium">' . $val . '</span>'
                : '<span class="text-red-500 line-through mx-1">' . $val . '</span>';
        }, $text);

        // ── 6) Yolg'iz qolgan qavslar
        $text = str_replace(['{', '}', '[', ']'], '', $text);

        // ── 7) Paragraflar
        $parts = preg_split("/\R{2,}/u", trim($text));
        $html  = '';

        foreach ($parts as $p) {
            $p = trim($p);
            if ($p === '') {
                continue;
            }

            $p = preg_replace("/\R/u", "<br>", $p);
            $html .= '<p class="mb-4 leading-7">' . $p . '</p>';
        }

        return $html;
    }

    public function mergeTaskResponses($task_response_1, $task_response_2)
    {
        $merged = [];

        $keys = [
            'task_achievement',
            'lexical_resource',
            'coherence_and_cohesion',
            'grammatical_range_and_accuracy',
        ];

        foreach ($keys as $key) {

            $text1 = isset($task_response_1[$key])
                ? trim($task_response_1[$key])
                : 'no result';

            $text2 = isset($task_response_2[$key])
                ? trim($task_response_2[$key])
                : 'no result';

            $combined = '';

            if ($text1 !== '') {
                $combined .= "Task 1.1\n" . $text1 . "\n";
            }

            if ($text2 !== '') {
                $combined .= "Task 1.2\n" . $text2;
            }

            $merged[$key] = trim($combined);
        }

        return $merged;
    }
    public function getWritingPersentageData($writingResult)
    {
        $all = (int) ($writingResult->max_score ?? 1);
        $score=$writingResult->partOneAnswer?->score_1+$writingResult->partOneAnswer?->score_2 + $writingResult->partTwoAnswer?->score;
        $correct = (int) (CefrTypeEnum::WRITING_BALL[$writingResult->category_type][rtrim(rtrim(number_format($score, 1, '.', ''), '0'), '.') ] ?? 0);
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

        return [
            'percent' => $percent,        // masalan: 72
            'array'   => $circumference,  // 289
            'offset'  => $offset,         // masalan: 81
            'degree'  => $this->generateDegreeWriting($correct),
            'ball'    => $correct,
        ];
    }

    public function getTasksData($writing_exercise)
    {
        $tasks = [];

        if ($writing_exercise->task_one) {
            $tasks['task_one'] = 'Task One';
        }

        if ($writing_exercise->task_two) {
            $tasks['task_two'] = 'Task Two';
        }


        return $tasks;
    }

    public function getEmptyTaskResponse()
    {
        return [
            'task_achievement'=>'no result',
            'grammatical_range_and_accuracy'=>'no result',
            'lexical_resource'=>'no result',
            'coherence_and_cohesion'=>'no result',
        ];
    }

    public function valid_result_json($result_json)
    {

    }
    public function generateDegreeWriting($ball)
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
    protected function textToHtml(?string $text): string
    {
        if (blank($text)) {
            return '';
        }

        // \r\n larni normalizatsiya qilamiz
        $text = str_replace("\r\n", "\n", trim($text));

        // Ikki va undan ortiq \n = yangi paragraf
        $paragraphs = preg_split('/\n{2,}/', $text);

        return collect($paragraphs)
            ->map(fn ($p) => '<p>' . nl2br(e($p), false) . '</p>')
            ->implode('');
    }
}
