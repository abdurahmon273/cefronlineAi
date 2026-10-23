<?php

namespace App\Services;

use App\Enums\CefrTypeEnum;
use App\Models\GlobalSetting;
use App\Models\SpeakingPrompt;
use App\Models\WritingPrompt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SpeakingAiService
{


    protected string $PartOneOneInputPrompt,
                     $PartOneOneOutputPrompt;
    protected string $PartOneTwoInputPrompt,
                     $PartOneTwoOutputPrompt;
    protected string $PartTwoInputPrompt,
                     $PartTwoOutputPrompt;
    protected string $PartThreeInputPrompt,
                     $PartThreeOutputPrompt;

    public function __construct()
    {

        $writingPrompts = SpeakingPrompt::query()->get();

        $part11 = $writingPrompts->firstWhere('type', 1);
        $part12 = $writingPrompts->firstWhere('type', 2);
        $part2 = $writingPrompts->firstWhere('type', 3);
        $part3 = $writingPrompts->firstWhere('type', 4);

        $this->PartOneOneInputPrompt  = (string) ($part11->input_prompt_text  ?? '');
        $this->PartOneOneOutputPrompt = (string) ($part11->output_prompt_text ?? '');

        $this->PartOneTwoInputPrompt  = (string) ($part12->input_prompt_text  ?? '');
        $this->PartOneTwoOutputPrompt = (string) ($part12->output_prompt_text ?? '')
        ;
        $this->PartTwoInputPrompt  = (string) ($part2->input_prompt_text  ?? '');
        $this->PartTwoOutputPrompt = (string) ($part2->output_prompt_text ?? '');

        $this->PartThreeInputPrompt  = (string) ($part3->input_prompt_text  ?? '');
        $this->PartThreeOutputPrompt = (string) ($part3->output_prompt_text ?? '');
    }

    public function GeneratePartOneFirstResult($partOneFirstResult,$partOneFirstExercise): array
    {
        $questionData=[];

        foreach ($partOneFirstExercise->questions as $question ){

            $this_answer=$partOneFirstResult->answers->where('question_id',$question->id)->first();

            $questionData[]=[
                'id'=>$question->id,
                'question'=>$question->question,
                'audio_url'=>$this_answer?->audio_path,
                'audio_text'=>$this_answer?->audio_text,
            ];
        }


        $result = $this->generateSpeakingResultByQuestions($questionData,$this->PartOneOneInputPrompt,$this->PartOneOneOutputPrompt,'1_1');

        foreach ($partOneFirstResult->answers as $key=>$answer){
            if (isset($result['result_json']['reGeneratedText']['question_'.($key+1)])){
                $answer->update([
                    'audio_text'=>$result['result_json']['reGeneratedText']['question_'.($key+1)]
                ]);
            }
        }

        return [
            'result' => $result,
        ];
    }

    public function GeneratePartOneSecondResult($partOneFirstResult,$partOneFirstExercise): array
    {
        $questionData=[];

        foreach ($partOneFirstExercise->questions as $question ){

            $this_answer=$partOneFirstResult->answers->where('question_id',$question->id)->first();

            $questionData[]=[
                'id'=>$question->id,
                'question'=>$question->question,
                'audio_url'=>$this_answer?->audio_path,
                'audio_text'=>$this_answer?->audio_text,
            ];
        }


        $result = $this->generateSpeakingResultByQuestions($questionData,$this->PartOneTwoInputPrompt,$this->PartOneTwoOutputPrompt,'1_2');

        foreach ($partOneFirstResult->answers as $key=>$answer){
            if (isset($result['result_json']['reGeneratedText']['question_'.($key+1)])){
                $answer->update([
                    'audio_text'=>$result['result_json']['reGeneratedText']['question_'.($key+1)]
                ]);
            }
        }

        return [
            'result' => $result,
        ];
    }


    public function GeneratePartTwoResult($partTwoResult,$partTwoExercise)
    {

        $questionData=[];

        if ($partTwoExercise?->question && $partTwoResult){
            $audio_text=$partTwoResult->audio_text;

            if (empty($audio_text)){
                return $this->generateZeroResult(2);
            }

            $questionData=[
                'id'=>$partTwoExercise->question->id,
                'question'=>$partTwoExercise->question->question,
                'audio_url'=>$partTwoResult->audio_path,
                'audio_text'=>$audio_text,
            ];
        }else{
            return $this->generateZeroResult(2);
        }


        $result = $this->generateSpeakingResultByQuestion($questionData,$this->PartTwoInputPrompt,$this->PartTwoOutputPrompt,'2');

        $partTwoResult->update([
            'audio_text'=>$result['result_json']['reGeneratedText']['answer_text']
        ]);


        return [
            'result' => $result,
        ];
    }
    public function GeneratePartThreeResult($partThreeResult,$partThreeExercise)
    {

        $questionData=[];

        if ($partThreeExercise){
            $audio_text=$partThreeResult->audio_text;

            if (empty($audio_text)){
                return $this->generateZeroResult(2);
            }

            $questionData=[
                'id'=>$partThreeExercise->id,
                'question'=>$partThreeExercise->question_text,
                'audio_url'=>$partThreeResult->audio_path,
                'audio_text'=>$audio_text,
            ];
        }else{
            return $this->generateZeroResult(2);
        }


        $result = $this->generateSpeakingResultByQuestion($questionData,$this->PartThreeInputPrompt,$this->PartThreeOutputPrompt,'3');

        $partThreeResult->update([
            'audio_text'=>$result['result_json']['reGeneratedText']['answer_text']
        ]);


        return [
            'result' => $result,
        ];
    }

    public function generateSpeakingResultByQuestions($questionData=[] ,$inputPrompt,$outputPrompt,$type)
    {
        $service=new \App\Services\ChatGPTService();

        $inputText ="Part ".$type." \n\n";

        foreach ($questionData as $key=>$question){
            $thisText="question_".($key+1)."=>[ \n".
                      "Question: '".$question['question']."'\n".
                      "Answer: '{$question['audio_text']}' ]\n\n ";

            $inputText=$inputText.$thisText;
        }

        $instructions =
            "=== RUBRIC & RULES ===\n" .
            trim($inputPrompt) . "\n\n" .
            "CRITICAL:\n" .
            trim($outputPrompt) . "\n\n" ;

        $schema = [
            'type' => 'object',
            'additionalProperties' => false,
            'properties' => [
                'score' => [
                    'type' => 'string',
                    'enum' => ['0','0.5','1','1.5','2','2.5','3','3.5','4','4.5','5'],
                ],
                'description' => [
                    'type' => 'string',
                    'minLength' => 1,
                ],
                'reGeneratedText' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'properties' => [
                        'question_1' => [
                            'type' => 'string',
                            'minLength' => 1,
                        ],
                        'question_2' => [
                            'type' => 'string',
                            'minLength' => 1,
                        ],
                        'question_3' => [
                            'type' => 'string',
                            'minLength' => 1,
                        ],
                    ],
                    'required' => [
                        'question_1',
                        'question_2',
                        'question_3',
                    ],
                ],
            ],
            'required' => [
                'score',
                'description',
                'reGeneratedText',
            ],
        ];

        $json =$service->ResponseByText($instructions,$inputText,$schema);

        return $json;
    }

    public function generateSpeakingResultByQuestion($questionData=[] ,$inputPrompt,$outputPrompt,$type)
    {
        $service=new \App\Services\ChatGPTService();

        $audio_text=$questionData['audio_text'];
        $question=$questionData['question'];
        $inputText ="Part ".$type." \n\n" .
                    "Question: '".$question."',\n".
                    "Answer: '{$audio_text}' ]\n\n ";



        $instructions =
            "=== RUBRIC & RULES ===\n" .
            trim($inputPrompt) . "\n\n" .
            "CRITICAL:\n" .
            trim($outputPrompt) . "\n\n" ;

        $schema = [
            'type' => 'object',
            'additionalProperties' => false,
            'properties' => [
                'score' => [
                    'type' => 'string',
                    'enum' => ['0','0.5','1','1.5','2','2.5','3','3.5','4','4.5','5'],
                ],
                'description' => [
                    'type' => 'string',
                    'minLength' => 1,
                ],
                'reGeneratedText' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'properties' => [
                        'answer_text' => [
                            'type' => 'string',
                            'minLength' => 1,
                        ]
                    ],
                    'required' => [
                        'answer_text',
                    ],
                ],
            ],
            'required' => [
                'score',
                'description',
                'reGeneratedText',
            ],
        ];

        $json =$service->ResponseByText($instructions,$inputText,$schema);

        return $json;
    }



    public function generateZeroResult($type)
    {
        if ($type==1) {
            return ['result'=>[
               'result_json' => [
                   'score' => 0,
                   'description' => "No user response",
                   'reGeneratedText' => [
                      'question_1' => "No user response",
                      'question_2' => "No user response",
                      'question_3' => "No user response",
                   ],
               ],
                'usage'=>[
                   'input'=>0,
                   'output'=>0,
                ]

            ]];
        }elseif ($type==2) {
            return [

   'result' => [
                'result_json' => [
                    'score' => 0,
                    'description' => "No user response",
                    'reGeneratedText' => [
                        'answer_text' => "No user response",
                    ],
                ],
                'usage'=>[
                    'input'=>0,
                    'output'=>0,
                ]

                ]
            ];
        }
    }

}
