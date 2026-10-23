<?php

namespace App\Services;

use App\Enums\CefrTypeEnum;
use App\Models\WritingPrompt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WritingAiService
{
    protected string $openaiUrl = 'https://api.openai.com/v1/responses';
    protected string $openaiKey;
    protected string $model;
    protected float  $temperature;
    protected int    $maxTokens;

    protected string $TaskOneOneInputPrompt;
    protected string $TaskOneOneOutputPrompt;
    protected string $TaskOneTwoInputPrompt;
    protected string $TaskOneTwoOutputPrompt;
    protected string $TaskTwoOutputPrompt;
    protected string $TaskTwoInputPrompt;

    public function __construct()
    {
        // ✅ tavsiya: config yoki GlobalSetting dan oling
        $this->openaiKey = (string) env("OPENAI_API_KEY", "");
        $this->model = 'gpt-5'; // yoki gpt-5-mini
        $this->temperature = 0.2;
        $this->maxTokens = 800;

        $writingPrompts = WritingPrompt::query()->get();

        $task11 = $writingPrompts->firstWhere('type', 1);
        $task12 = $writingPrompts->firstWhere('type', 2);
        $task2 = $writingPrompts->firstWhere('type', 3);

        $this->TaskOneOneInputPrompt  = (string) ($task11->input_prompt_text  ?? '');
        $this->TaskOneOneOutputPrompt = (string) ($task11->output_prompt_text ?? '');

        $this->TaskOneTwoInputPrompt  = (string) ($task12->input_prompt_text  ?? '');
        $this->TaskOneTwoOutputPrompt = (string) ($task12->output_prompt_text ?? '')
        ;
        $this->TaskTwoInputPrompt  = (string) ($task2->input_prompt_text  ?? '');
        $this->TaskTwoOutputPrompt = (string) ($task2->output_prompt_text ?? '');
    }

    public function GeneratePartOneFirstResult(string $question, string $text): array
    {
        $regeneratedQ=$this->reGenerateQuestion($question);

        $question_1=$regeneratedQ['question_1'];

        $result = $this->generateWritingResult($question_1, $text,$this->TaskOneOneInputPrompt,$this->TaskOneOneOutputPrompt,'1_1');

        return [
            'result' => $result,
        ];
    }
    public function GeneratePartOneSecondResult(string $question, string $text): array
    {
        $regeneratedQ=$this->reGenerateQuestion($question);

        $question_2=$regeneratedQ['question_2'];

        $result = $this->generateWritingResult($question_2, $text,$this->TaskOneTwoInputPrompt,$this->TaskOneTwoOutputPrompt,'1_2');

        return [
            'result' => $result,
        ];
    }

    public function GeneratePartTwoResult(string $question, string $text)
    {
        $json= $this->generateWritingResult($question,$text,$this->TaskTwoInputPrompt,$this->TaskTwoOutputPrompt,'2');

        return $json;
    }
    public function generateWritingResult(string $question, string $text,$inputPrompt,$outputPrompt,$type): array
    {
        $service=new \App\Services\ChatGPTService();

        $inputText =
            "QUESTION:\n{$question}\n\n" .
            "Task {$type} ANSWER:\n{$text}\n\n" ;

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
                    'type' => 'number',
                    'enum' => CefrTypeEnum::WRITING_DEGREE_ENUM[$type],
                ],
                'description' => [
                    'type' => 'string',
                    'minLength' => 1,
                ],
                'task_response' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'properties' => [
                        'task_achievement' => ['type' => 'string', 'minLength' => 1],
                        'grammatical_range_and_accuracy' => ['type' => 'string', 'minLength' => 1],
                        'lexical_resource' => ['type' => 'string', 'minLength' => 1],
                        'coherence_and_cohesion' => ['type' => 'string', 'minLength' => 1],
                    ],
                    'required' => [
                        'task_achievement',
                        'grammatical_range_and_accuracy',
                        'lexical_resource',
                        'coherence_and_cohesion'
                    ],
                ],
                'reGeneratedText' => [
                    'type' => 'string',
                    'minLength' => 1,
                ],
            ],
            'required' => ['score', 'description', 'task_response', 'reGeneratedText'],
        ];

        $json =$service->ResponseByText($instructions,$inputText,$schema);

        return $json;

    }

    public function reGenerateQuestion($question)
    {
        $parts = explode('Task 1.1', $question);

        $commonText = trim($parts[0]); // umumiy qism

        $taskParts = explode('Task 1.2', $parts[1] ?? '');

        $taskOneText = trim($taskParts[0] ?? '');
        $taskTwoText = trim($taskParts[1] ?? '');

        $question_1 = $commonText . "\n\nTask 1.1\n\n" . $taskOneText;

        $question_2 = $commonText . "\n\nTask 1.2\n\n" . $taskTwoText;

        return [
            'question_1' => $question_1,
            'question_2' => $question_2,
        ];
    }
    public function generateDegree($ball)
    {
        if (intval($ball) < 0 || intval($ball) > 75) {
            return 'Invalid score';
        }

        if (intval($ball) <= 37) {
            return 'Below B1';
        } elseif (intval($ball) <= 50) {
            return 'B1';
        } elseif (intval($ball) <= 64) {
            return 'B2';
        } else { // 65–75
            return 'C1';
        }
    }


}
