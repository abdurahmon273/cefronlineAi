<?php

namespace App\Services;

use App\Enums\CefrTypeEnum;
use App\Http\Resources\V2\Full\LastResultsResource;
use App\Jobs\WritingCheck;
use App\Models\FullExercise;
use App\Models\FullResult;
use App\Models\WritingPrompt;
use App\Traits\ListeningGetDataTrait;
use App\Traits\ReadingGetDataTrait;
use App\Traits\SpeakingGetDataTrait;
use App\Traits\TestStoreHelperTrait;
use App\Traits\WritingGetDataTrait;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FullMockService
{

    use TestStoreHelperTrait,ListeningGetDataTrait,ReadingGetDataTrait,WritingGetDataTrait,SpeakingGetDataTrait;

    public function __construct()
    {

    }

    public function firstFullMockData($id)
    {
        $fullExercise = FullExercise::with([
            'listening_exercise',
            'reading_exercise',
            'writing_exercise',
            'speaking_exercise'
        ])->find($id);

        if ($fullExercise) {
            $results = FullResult::where('full_exercise_id', $fullExercise->id)
                ->where('user_id', auth()->id())
                ->orderByDesc('id')
                ->limit(4)
                ->get();

            $lastResult = $results->first();           // 0 ta bo'lsa null
            $lastResults = $results->skip(1)->values(); // qolganlari (0–3 ta)

            $fullResult = $lastResult && $lastResult->status == 0 ? $lastResult : null;

            $exerciseData = $this->generateExerciesData($fullExercise, $fullResult);

            return [
                'id' => $fullExercise->id,
                'title' => $fullExercise->title,
                'type' => CefrTypeEnum::FULL_MOCK->value,
                'exerciseData' => $exerciseData,
                'resultExists' => $fullResult ? 1 : 0,
                'lastResult' => $lastResult,   // null yoki 1 ta
                'lastResults' => $lastResults, // 0–3 ta
            ];
        }

        abort(404);
    }

    public function generateExerciesData($fullExercise,$fullResult)
    {
        $exerciseData=[];

        $listeningAccess=$this->generateListenignAccess($fullExercise->listening_exercise);

        if ($listeningAccess['status']){
            $exerciseData[]=[
                'id' => $fullExercise->listening_exercise->id,
                'title' => CefrTypeEnum::CEFR_TYPE_NAMES[CefrTypeEnum::LISTENING->value],
                'type'=>CefrTypeEnum::LISTENING->value,
                'image'=>CefrTypeEnum::CEFR_IMAGE_DATA[CefrTypeEnum::LISTENING->value],
                'difficulty'=>'medium',
                'time_limit'=>60,
                'buttonStatus'=>$fullResult?->listening_result ? 1 : 0,
                'buttonLink'=>route('user.full.listening.start',['id'=>$fullExercise->listening_exercise->id]),
                'button'=>[]
            ];
        }

        $readingAccess=$this->generateReadingAccess($fullExercise->reading_exercise);

        if ($readingAccess['status']){
            $exerciseData[]=[
                'id' => $fullExercise->reading_exercise->id,
                'title' => CefrTypeEnum::CEFR_TYPE_NAMES[CefrTypeEnum::READING->value],
                'type'=>CefrTypeEnum::READING->value,
                'image'=>CefrTypeEnum::CEFR_IMAGE_DATA[CefrTypeEnum::READING->value],
                'difficulty'=>'medium',
                'time_limit'=>60,
                'buttonStatus'=>$fullResult?->reading_result ? 1 : 0,
                'buttonLink'=>route('user.full.reading.start',['id'=>$fullExercise->reading_exercise->id]),
            ];
        }
        $writingAccess=$this->generateWritingAccess($fullExercise->writing_exercise);

        if ($writingAccess['status']){
            $exerciseData[]=[
                'id' => $fullExercise->writing_exercise->id,
                'title' => CefrTypeEnum::CEFR_TYPE_NAMES[CefrTypeEnum::WRITING->value],
                'type'=>CefrTypeEnum::WRITING->value,
                'image'=>CefrTypeEnum::CEFR_IMAGE_DATA[CefrTypeEnum::WRITING->value],
                'difficulty'=>'medium',
                'time_limit'=>60,
                'buttonStatus'=>$fullResult?->writing_result ? 1 : 0,
                'buttonLink'=>route('user.full.writing.start',['id'=>$fullExercise->writing_exercise->id]),
            ];
        }

        $speakingAccess=$this->generateSpeakingAccess($fullExercise->speaking_exercise);

        if ($speakingAccess['status']){
            $exerciseData[]=[
                'id' => $fullExercise->speaking_exercise->id,
                'title' => CefrTypeEnum::CEFR_TYPE_NAMES[CefrTypeEnum::SPEAKING->value],
                'type'=>CefrTypeEnum::SPEAKING->value,
                'image'=>CefrTypeEnum::CEFR_IMAGE_DATA[CefrTypeEnum::SPEAKING->value],
                'difficulty'=>'medium',
                'time_limit'=>60,
                'buttonStatus'=>$fullResult?->speaking_result ? 1 : 0,
                'buttonLink'=>route('user.full.speaking.start',['id'=>$fullExercise->speaking_exercise->id]),
            ];
        }

        return $exerciseData;
    }

    public function getFullMockResultData($id,$type)
    {
        $fullResult=FullResult::with(['listening_result','reading_result','writing_result','speaking_result'])->find($id);
        $listeningData=[];
        $readingData=[];
        $writingData=[];
        $speakingData=[];
        $testTypes=[];

        if($fullResult->listening_result){
            $listeningData=$this->GetListeningResultData($fullResult->listening_result->id);
            $testTypes[]='Listening';
        }

        if ($fullResult->reading_result){
            $readingData=$this->GetReadingResultData($fullResult->reading_result->id);
            $testTypes[]='Reading';
        }

        if ($fullResult->writing_result){
            $writingData=$this->GetWritingResultData($fullResult->writing_result->id);
            $testTypes[]='Writing';
        }

        if ($fullResult->speaking_result){
            $speakingData=$this->GetSpeakingResultData($fullResult->speaking_result->id);
            $testTypes[]='Speaking';
        }


        return [
            'id'=>$fullResult->id,
            'exercise_id'=>$fullResult->full_exercise_id,
            'listeningData'=>$listeningData,
            'readingData'=>$readingData,
            'writingData'=>$writingData,
            'speakingData'=>$speakingData,
            'testTypes'=>$testTypes,
            'type'=>$type
        ];
    }

    public function getFullResultData($full_exercise_id)
    {
        $fullExercise = FullExercise::with([
            'listening_exercise',
            'reading_exercise',
            'writing_exercise',
            'speaking_exercise'
        ])->find($full_exercise_id);

        if ($fullExercise) {
            $results = FullResult::where('full_exercise_id', $fullExercise->id)
                ->where('user_id', auth('sanctum')->user()->id)
                ->orderByDesc('id')
                ->limit(4)
                ->get();

            $lastResult = $results->first();           // 0 ta bo'lsa null
            $lastResults = $results->skip(1)->values(); // qolganlari (0–3 ta)
            $exercise_data=[
                'listening'=>true,
                'reading'=>true,
                'writing'=>true,
                'speaking'=>true,
            ];
            $lastResultarray=null;

            if($lastResult){
                $lastResultarray = [
                    'id' => $lastResult->id,
                    'created_at' => $lastResult->created_at,
                    'status' => intval($lastResult->status),
                ];

                if (!$lastResult->status){
                    $exercise_data=[
                        'listening'=>$lastResult->listening_result_id ? false : true,
                        'reading'=>$lastResult->reading_result_id ? false : true,
                        'writing'=>$lastResult->writing_result_id ? false : true,
                        'speaking'=>$lastResult->speaking_result_id ? false : true,
                    ];
                }

            }


            return [
                'id' => $fullExercise->id,
                'reset_status'=>$lastResult ? ($lastResult->status ? false : true) : false,
                'lastResult' => $lastResultarray,
                'lastResults' =>count($lastResults)>0 ?  LastResultsResource::collection($lastResults) : null,
                'exercise_data'=>$exercise_data
                ];

        }

        abort(404);

    }
}
