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
use App\Models\SpeakingExercise;
use App\Models\SpeakingPartOneFirst;
use App\Models\SpeakingPartOneSecond;
use App\Models\SpeakingPartThree;
use App\Models\SpeakingPartTwo;
use App\Models\SpeakingResult;
use App\Models\WritingResult;
use Illuminate\Support\Facades\Storage;

trait SpeakingGetDataTrait
{
    public function GetSpeakingData($id)
    {
        $speaking_exercise=SpeakingExercise::where('id',$id)
            ->with(['part_one_first','part_one_second','part_two','part_three'])->first();

        $part_category='';
        if (isset(SpeakingExercise::PART_CATEGORIES[$speaking_exercise->part_category])){
            $part_category=SpeakingExercise::PART_CATEGORIES[$speaking_exercise->part_category];
        }

        $audioInstructionsData=$this->generateAudioInstructionsData();


        return [
            'id'=>$speaking_exercise->id,
            'mock_id'=>$speaking_exercise->full_exercise? $speaking_exercise->full_exercise->id : 0,
            'speakingExercise'=>$speaking_exercise,
            'name'=>$speaking_exercise->title,
            'status'=>$speaking_exercise->status,
            'type' => CefrTypeEnum::SPEAKING->value,
            'part_category'=>$part_category,
            'part_category_number'=>$speaking_exercise->part_category,
            'is_mock'=>($speaking_exercise->part_category == '4' ||$speaking_exercise->full_exercise )  ? 1 : 0,
            'audioInstructionsData'=>$audioInstructionsData,
            'part_one_first'=>$this->GetSpeakingPartOneFirstData($speaking_exercise->part_one_first ),
            'part_one_second'=>$this->GetSpeakingPartOneSecondData($speaking_exercise->part_one_second ),
            'part_two'=>$this->GetSpeakingPartTwoData($speaking_exercise->part_two ),
            'part_three'=>$this->GetSpeakingPartThreeData($speaking_exercise->part_three ),
        ];
    }

    public function GetSpeakingPartOneFirstData($part_one_first)
    {
        if ($part_one_first){
            $part_one_first=SpeakingPartOneFirst::where('id',$part_one_first->id)->with(['questions'])->first();

            $questions=[];

            foreach ($part_one_first->questions as $question){
                $questions[]=[
                    'id'=>$question->id,
                    'question'=>$question->question,
                    'record_time'=>$question->time,
                    'wait_time'=>5,
                    'audio_link'=> $question->audio_url ? route('admin.speaking.audio.stream',$question->audio_url) : null,
                ];
            }

            return [
                'id'=>$part_one_first->id,
                'title'=>$part_one_first->title ?? '',
                'instructionAudioLink'=>asset('assets/audios/speaking_instruction_part_1_1.mp3'),
                'questions'=> $questions,
            ];
        }else{
            return null;
        }
    }
    public function GetSpeakingPartOneSecondData($part_one_second)
    {
        if ($part_one_second){
            $part_one_second=SpeakingPartOneSecond::where('id',$part_one_second->id)->with(['questions','images'])->first();

            $questions=[];
            $images=[];

            foreach ($part_one_second->questions as $key => $question){
                $questions[]=[
                    'id'=>$question->id,
                    'question'=>$question->question,
                    'record_time'=>$key ==0 ? 45 : $question->time,
                    'wait_time'=>$key == 0 ? 10 : 5,
                    'audio_link'=>$question->audio_url ? route('admin.speaking.audio.stream',$question->audio_url) : null,
                ];
            }

            foreach ($part_one_second->images as $image){
                $images[]=[
                    'id'=>$image->id,
                    'url'=>Storage::url('public/speaking_images/'.$image->image)
                ];
            }

            return [
                'id'=>$part_one_second->id,
                'title'=>$part_one_second->title ?? '',
                'instructionAudioLink'=>asset('assets/audios/speaking_instruction_part_1_2.mp3'),
                'questions'=> $questions,
                'images'=>$images,
            ];
        }else{
            return null;
        }
    }
    public function GetSpeakingPartTwoData($part_two)
    {
        if ($part_two){
            $part_two=SpeakingPartTwo::where('id',$part_two->id)->with(['question','images'])->first();

            if ($part_two->question){

            $question=[
                'id'=>$part_two->question->id,
                'question'=>$part_two->question->question,
                'record_time'=>$part_two->question->time ?? 120,
                'wait_time'=>60,
                'audio_link'=>null,
//                'audio_link'=>route('admin.speaking.audio.stream',$part_two->question->audio_url),
            ];

            $images=[];

            foreach ($part_two->images as $image){
                $images[]=[
                    'id'=>$image->id,
                    'url'=>Storage::url('public/speaking_images/'.$image->image)
                ];
            }

            return [
                'id'=>$part_two->id,
                'title'=>$part_two->title ?? '',
                'instructionAudioLink'=>asset('assets/audios/speaking_instruction_part_2.mp3'),
                'question'=> $question,
                'images'=>$images,
            ];
            }else{
                return null;
            }

        }else{
            return null;
        }
    }
    public function GetSpeakingPartThreeData($part_three)
    {
        if ($part_three){
            $part_three=SpeakingPartThree::where('id',$part_three->id)->with(['questions'])->first();

            return [
                'id'=>$part_three->id,
                'title'=>$part_three->title ?? '',
                'instructionAudioLink'=>asset('assets/audios/speaking_instruction_part_3.mp3'),
                'theme'=>$part_three->theme,
                'question_text'=> $part_three->question_text ,
                'time'=>$part_three->time,
                'wait_time'=>60,
                'audio_link'=>null,
//                'audio_link'=>route('admin.speaking.audio.stream',$part_three->theme_audio_url),
            ];
        }else{
            return null;
        }
    }

    public function generateAudioInstructionsData()
    {
        $tingVoiseLink=asset('assets/audios/ting_voise_v2.mp3');

        return [
            'tingVoiseLink'=>$tingVoiseLink,
        ];
    }

    public function speakingResultAudioUrl($filename)
    {
        if (!$filename) {
            return null;
        }

        return route('admin.speaking.result.audio.stream', $filename);
    }

    public function GetSpeakingResultApiData($id)
    {
        $speakingResult = SpeakingResult::with(['part_one_first.answers','part_one_second.answers','part_two','part_three','exercise.part_one_first.questions','exercise.part_one_second.questions','exercise.part_one_second.images','exercise.part_two','exercise.part_three'])->find($id);

        // shu id bo'yicha natija bo'lmasa — null->exercise xatosi o'rniga 404
        abort_if(!$speakingResult, 404, 'Speaking result not found');

        $speakingExercise=$speakingResult->exercise;
        $percentageData=$this->getSpeakingPersentageData($speakingResult);

        $createdText = \Illuminate\Support\Carbon::parse($speakingResult->created_at)
            ->locale('uz')
            ->translatedFormat('j F, H:i');
        $tabs=$this->getSpeakingPartsData($speakingExercise);
        $status=$speakingResult->status;
        return [
            'id'=>$id,
            'name'=>$speakingExercise->title ?? '',
//            'speakingResult'=>$speakingResult,
            'createdText'=>$createdText,
            'tabs'=>$tabs,
            'active'=>array_keys($tabs)[0],
            'status'=>$status,
            'download_link' => $status==3 ? ( route('api.user.results.DownloadResultPdf', [$speakingResult->id, \App\Enums\CefrTypeEnum::SPEAKING->value,'token'=>'wgp8kbrymkuaZEwNcAaMfQbTHFQmzKB6W57OecGW']) ): null,
            'percentageData' => $percentageData,
            'part_one_first'=>$this->generatePartOneFirstData($speakingExercise->part_one_first,$speakingResult->part_one_first,$status,1),
            'part_one_second'=>$this->generatePartOneFirstData($speakingExercise->part_one_second,$speakingResult->part_one_second,$status,2),
            'part_two'=>$this->generatePartTwoData($speakingExercise->part_two,$speakingResult->part_two,$status),
            'part_three'=>$this->generatePartThreeData($speakingExercise->part_three,$speakingResult->part_three,$status),
            'feedbackCategories'=>['Pronunciation', 'Fluency and Coherence', 'Lexical Resource', 'Grammatical Range and Accuracy']
        ];
    }

    public function GetSpeakingResultData($id)
    {
        $speakingResult = SpeakingResult::with(['part_one_first.answers','part_one_second.answers','part_two','part_three','exercise.part_one_first.questions','exercise.part_one_second.questions','exercise.part_one_second.images','exercise.part_two','exercise.part_three'])->find($id);
        $speakingExercise=$speakingResult->exercise;
        $percentageData=$this->getSpeakingPersentageData($speakingResult);

        $createdText = \Illuminate\Support\Carbon::parse($speakingResult->created_at)
            ->locale('uz')
            ->translatedFormat('j F, H:i');
        $tabs=$this->getSpeakingPartsData($speakingExercise);
        $status=$speakingResult->status;
        return [
            'id'=>$id,
            'speakingResult'=>$speakingResult,
            'createdText'=>$createdText,
            'tabs'=>$tabs,
            'active'=>array_keys($tabs)[0],
            'status'=>$status,
            'percentageData' => $percentageData,
            'part_one_first'=>$this->generatePartOneFirstData($speakingExercise->part_one_first,$speakingResult->part_one_first,$status,1),
            'part_one_second'=>$this->generatePartOneFirstData($speakingExercise->part_one_second,$speakingResult->part_one_second,$status,2),
            'part_two'=>$this->generatePartTwoData($speakingExercise->part_two,$speakingResult->part_two,$status),
            'part_three'=>$this->generatePartThreeData($speakingExercise->part_three,$speakingResult->part_three,$status),
            'feedbackCategories'=>['Pronunciation', 'Fluency and Coherence', 'Lexical Resource', 'Grammatical Range and Accuracy']
        ];
    }

    public function generatePartOneFirstData($part_one,$part_one_result,$status,$type)
    {
        if ($part_one && $part_one_result){

            $questions=[];
            $images=[];

            foreach ($part_one->questions as $question) {
                $answer=null;
                if ($part_one_result){
                $answer = $part_one_result->answers
                    ->where('question_id', $question->id)
                    ->first();
                }

                $questions[] = [
                    'id' => $question->id,
                    'question' => $question->question,
                    'answer_text' => $answer ? $this->reGenerateTextToHTML($answer->audio_text) : ' ',
                    'audio_link' => $answer
                        ? $this->speakingResultAudioUrl($answer->audio_path)
                        : null,
                ];
            }


            if ($part_one->images){
                foreach ($part_one->images as $image){
                    $images[]=[
                        'id'=>$image->id,
                        'url'=>Storage::url('public/speaking_images/'.$image->image)
                    ];
                }
            }

            $result_json=json_decode($part_one_result->result_json ?? '{}' ,true);

            $resultData=[
                'score'=>rtrim(rtrim(number_format($part_one_result->score, 1, '.', ''), '0'), '.') ,
                'max_score'=>CefrTypeEnum::SPEAKING_ADDITIONAL_MAX_SCORES[$type],
                'description'=>isset($result_json['description']) ? $result_json['description'] : '',
            ];

            return [
                'id'=>$part_one_result->id,
                'title'=>$part_one->title ?? '',
                'questions'=> $questions,
                'images'=>$images,
                'resultData'=>$resultData,
            ];
        }else{
            return null;
        }
    }

    public function generatePartTwoData($part_two,$part_two_result,$status)
    {
        if ( $part_two && $part_two_result ){
            $part_two=SpeakingPartTwo::where('id',$part_two->id)->with(['question','images'])->first();
            $question=[
                'id'=>$part_two->question->id,
                'question'=>$part_two->question->question,
                'answer_text' => '',
                'audio_link'=>null
            ];

            if ($part_two_result && $part_two_result->audio_path){
                $question['audio_link']=$this->speakingResultAudioUrl($part_two_result->audio_path);
                $question['answer_text']=$this->reGenerateTextToHTML($part_two_result->audio_text);
            }
            $images=[];

            foreach ($part_two->images as $image){
                $images[]=[
                    'id'=>$image->id,
                    'url'=>Storage::url('public/speaking_images/'.$image->image)
                ];
            }
            $result_json=json_decode($part_two_result->result_json?? '{}',true);

            $resultData=[
                'score'=>rtrim(rtrim(number_format($part_two_result->score, 1, '.', ''), '0'), '.') ,
                'max_score'=>CefrTypeEnum::SPEAKING_ADDITIONAL_MAX_SCORES[3],
                'description'=>isset($result_json['description']) ? $result_json['description'] : '',
            ];

            return [
                'id'=>$part_two->id,
                'title'=>$part_two->title ?? '',
                'question'=> $question,
                'images'=>$images,
                'resultData'=>$resultData,
            ];
        }else{
            return null;
        }

    }
    public function generatePartThreeData($part_three,$part_three_result,$status)
    {
        if ($part_three && $part_three_result) {
            $part_three=SpeakingPartThree::where('id',$part_three->id)->with(['questions'])->first();
            $audio_link=null;
            $audio_text=null;

            if ($part_three_result && $part_three_result->audio_path){
                $audio_link=$this->speakingResultAudioUrl($part_three_result->audio_path);
                $audio_text=$this->reGenerateTextToHTML($part_three_result->audio_text);
            }
            $forAgainstData=$this->generateForAgainstData($part_three->question_text);

            $result_json=json_decode($part_three_result->result_json?? '{}' ,true);

            $resultData=[
                'score'=>rtrim(rtrim(number_format($part_three_result->score, 1, '.', ''), '0'), '.') ,
                'max_score'=>CefrTypeEnum::SPEAKING_ADDITIONAL_MAX_SCORES[4],
                'description'=>isset($result_json['description']) ? $result_json['description'] : '',
            ];

            return [
                'id'=>$part_three->id,
                'title'=>$part_three->title ?? '',
                'theme'=>$part_three->theme,
                'question_text'=> $part_three->question_text ,
                'forAgainstData'=>$forAgainstData,
                'answer_text' => $audio_text,
                'audio_link'=>$audio_link,
                'resultData'=>$resultData,
            ];
        }else{
            return null;
        }

    }
    public function getSpeakingPartsData($speakingExercise)
    {
        $parts = [];

        if ($speakingExercise->part_one_first && $speakingExercise->part_one_second) {
            $parts['part_one'] = 'Part One';
        }

        if ($speakingExercise->part_two) {
            $parts['part_two'] = 'Part Two';
        }

        if ($speakingExercise->part_three) {
            $parts['part_three'] = 'Part Three';
        }


        return $parts;
    }

    public function getSpeakingPersentageData($speakingResult)
    {
        $all = CefrTypeEnum::SPEAKING_MAX_SCORES[$speakingResult->category_type];
        $correct = (int) (CefrTypeEnum::SPEAKING_BALL[$speakingResult->category_type][rtrim(rtrim(number_format($speakingResult->score, 1, '.', ''), '0'), '.') ] ?? 0);


        if ($all <= 0) {
            return [
                'percent' => 0,
                'array'   => 289,
                'offset'  => 289,
                'ball'=>0,
                'degree'=>null,
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
            'ball'=>$correct,
            'degree'=>$this->generateDegree1($correct),
        ];
    }

    public function generateDegree1($ball)
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
    public function generateForAgainstData($questionText): array
    {
        $text = (string) $questionText;
        $text = str_replace(["\r\n", "\r"], "\n", $text);

        $text = preg_replace('/[ \t]+/', ' ', $text); // collapse multiple spaces
        $text = trim($text);

        $forPos = null;
        $againstPos = null;

        if (preg_match('/(^|\n)\s*FOR\s*:?\s*(\n|$)/i', $text, $m, PREG_OFFSET_CAPTURE)) {
            $forPos = $m[0][1];
        }
        if (preg_match('/(^|\n)\s*AGAINST\s*:?\s*(\n|$)/i', $text, $m2, PREG_OFFSET_CAPTURE)) {
            $againstPos = $m2[0][1];
        }

        $forBlock = '';
        $againstBlock = '';

        if ($forPos !== null && $againstPos !== null) {
            if ($forPos < $againstPos) {
                $forBlock = substr($text, $forPos, $againstPos - $forPos);
                $againstBlock = substr($text, $againstPos);
            } else {
                $againstBlock = substr($text, $againstPos, $forPos - $againstPos);
                $forBlock = substr($text, $forPos);
            }
        } elseif ($forPos !== null) {
            $forBlock = substr($text, $forPos);
        } elseif ($againstPos !== null) {
            $againstBlock = substr($text, $againstPos);
        } else {
            $forBlock = $text;
        }

        $forBlock = preg_replace('/(^|\n)\s*FOR\s*:?\s*(\n|$)/i', "\n", $forBlock);
        $againstBlock = preg_replace('/(^|\n)\s*AGAINST\s*:?\s*(\n|$)/i', "\n", $againstBlock);

        $forItems = $this->extractBulletLines($forBlock);
        $againstItems = $this->extractBulletLines($againstBlock);

        return [
            'forDetails' => $this->renderBulletsHtml($forItems),
            'againtsDetails' => $this->renderBulletsHtml($againstItems),
        ];
    }

    private function extractBulletLines(string $block): array
    {
        $block = trim($block);
        if ($block === '') return [];

        $lines = array_map('trim', explode("\n", $block));
        $items = [];

        foreach ($lines as $line) {
            if ($line === '') continue;

            $clean = preg_replace('/^\s*(•|\-|\*|–|—|\d+\)|\d+\.)\s*/u', '', $line);
            $clean = trim($clean);

            if ($clean === '') continue;
            if (preg_match('/^(FOR|AGAINST)\s*:?\s*$/i', $clean)) continue;

            $items[] = $clean;
        }

        if (count($items) === 0 && $block !== '') {
            $fallback = preg_split('/\s*•\s*/u', $block);
            $fallback = array_filter(array_map('trim', $fallback));
            $items = array_values($fallback);
        }

        return $items;
    }

    private function renderBulletsHtml(array $items): string
    {
        if (empty($items)) return '';

        $lis = '';
        foreach ($items as $item) {
            $lis .= '<li>' . e($item) . '</li>';
        }

        return '<ul class="list-disc pl-5 space-y-1 text-sm text-gray-700">' . $lis . '</ul>';
    }


}
