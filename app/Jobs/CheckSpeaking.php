<?php

namespace App\Jobs;

use App\Enums\CefrTypeEnum;
use App\Models\SpeakingExercise;
use App\Models\SpeakingPartOneFirstQuestions;
use App\Models\SpeakingPartOneSecondQuestions;
use App\Models\SpeakingPartThree;
use App\Models\SpeakingPartThreeQuestions;
use App\Models\SpeakingPartTwoQuestions;
use App\Models\SpeakingResult;
use App\Models\User;
use App\Services\ChatGPTService;
use App\Services\DeepgramService;
use App\Services\SpeakingAiService;
use App\Traits\BillingTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CheckSpeaking implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels,BillingTrait;

    public int $resultId;
    public int $userId;
    public int $status , $type;

    // bitta job retry_after dan uzoq ishlab, queue uni "qotgan" deb qayta ishga tushirmasligi uchun
    public int $timeout = 600;
    public int $tries = 1;

    public function __construct($resultId, $userId,$status, $type)
    {
        $this->resultId = $resultId;
        $this->userId = $userId;
        $this->status = $status;
        $this->type = $type;
    }

    /**
     * Bir xil (result + status) job bir vaqtda ikki marta ishlamasin.
     * Dublikat kelsa — release qilmasdan tashlab yuboramiz (matnni buzib yubormasligi uchun).
     */
    public function middleware(): array
    {
        return [
            (new WithoutOverlapping("check-speaking-{$this->resultId}-{$this->status}"))
                ->dontRelease()
                ->expireAfter(900),
        ];
    }

    /**
     * Execute the job.
     */
    public function handle(SpeakingAiService $speakingAiService ,DeepgramService $deepgramService,ChatGPTService $chatGPTService): void
    {
        $speakingResult = SpeakingResult::with([
            'part_one_first.answers',
            'part_one_second',
            'part_two',
            'part_three',
            'exercise.part_one_first.questions',
            'exercise.part_one_second',
            'exercise.part_two.question',
            'exercise.part_three'
        ])->findOrFail($this->resultId);

        $speakingResult->update(['status' => 1]);

        try {
            $category=SpeakingResult::CATEGORY_TYPES[$speakingResult->category_type];

            if ( ($this->status==1 && $this->type) || ($this->status==1 && !$this->type) ||  ($this->status==3 && !$this->type)||  ($this->status==4 && !$this->type)  ){

//                Log::channel('telegram')->error(
//                    "\n\n Start Speaking Checking, ".$category.
//                    "\n Result id ".$this->resultId.
//                    "\n User id ".$this->userId
//                );
//                Log::channel('telegram2')->error(
//                    "\n\n Start Speaking Checking, ".$category.
//                    "\n Result id ".$this->resultId.
//                    "\n User id ".$this->userId
//                );
            }

            if ($this->status==1){

                foreach ($speakingResult->part_one_first->answers as $answer) {

                    if ($answer->audio_path){

                        // allaqachon muvaffaqiyatli transkripsiya qilingan bo'lsa — qayta yozmaymiz.
                        // (dublikat/retry job mavjud yaxshi matnni " " bilan almashtirib yubormasligi uchun)
//                        if (trim((string) $answer->audio_text) !== '') {
//                            continue;
//                        }

                        try {

                            $audio_data= $deepgramService->generateText($answer->audio_path);

                            $answer->update([
                                'audio_text'=>$audio_data['text'],
                            ]);
                        }catch (\Throwable $e) {

                            $mime = Storage::disk('private')->mimeType( "voiceAudios/$answer->audio_path") ?? 'application/octet-stream';

                            Log::channel('telegram')->error(
                                "\n\n Deepgram answer transcribe failed, ".$category.
                                "\n result_id ".$speakingResult->id.
                                "\n answer_id ".$answer->id.
                                "\n audio_path ".$answer->audio_path.
                                "\n mime ".$mime.
                                "\n message :  ".$this->escapeForTelegram($e->getMessage())
                            );

                            $answer->update([
                                'audio_text' => " ",
                            ]);

                            sleep(2);

                        }

                        sleep(1);
                    }


                }

            }elseif ($this->status==2){

                foreach ($speakingResult->part_one_second->answers as $answer) {

                    if ($answer->audio_path){

                        // allaqachon muvaffaqiyatli transkripsiya qilingan bo'lsa — qayta yozmaymiz.
//                        if (trim((string) $answer->audio_text) !== '') {
//                            continue;
//                        }

                        try {

                            $audio_data= $deepgramService->generateText($answer->audio_path);

                            $answer->update([
                                'audio_text'=>$audio_data['text'],
                            ]);
                        }catch (\Throwable $e) {
                            $mime = Storage::disk('private')->mimeType( "voiceAudios/$answer->audio_path") ?? 'application/octet-stream';

                            Log::channel('telegram')->error(
                                "\n\n Deepgram answer transcribe failed, ".$category.
                                "\n result_id ".$speakingResult->id.
                                "\n answer_id ".$answer->id.
                                "\n audio_path ".$answer->audio_path.
                                "\n mime ".$mime.
                                "\n message :  ".$this->escapeForTelegram($e->getMessage())
                            );

                            $answer->update([
                                'audio_text' => " ",
                            ]);

                            sleep(2);
                        }

                        sleep(1);
                    }

                }

            }elseif ($this->status==3){
                $part_two_result=$speakingResult->part_two;

                if ( $part_two_result && $part_two_result->audio_path ){
                    try {


                    $audio_data= $deepgramService->generateText($part_two_result->audio_path);

                    $part_two_result->update([
                        'audio_text'=>$audio_data['text'],
                    ]);

                    }catch (\Throwable $e) {
                        $mime = Storage::disk('private')->mimeType( "voiceAudios/$part_two_result->audio_path") ?? 'application/octet-stream';

                        Log::channel('telegram')->error(
                            "\n\n Deepgram answer transcribe failed, ".$category.
                            "\n result_id ".$speakingResult->id.
                            "\n answer_id ".$part_two_result->id.
                            "\n audio_path ".$part_two_result->audio_path.
                            "\n mime ".$mime.
                            "\n message :  ".$this->escapeForTelegram($e->getMessage())
                        );

                        $part_two_result->update([
                            'audio_text' => " ",
                        ]);

                        sleep(5);
                    }
                }

            }elseif ($this->status==4){

                $part_three_result=$speakingResult->part_three;

                if ( $part_three_result && $part_three_result->audio_path ){

                    try {

                    $audio_data= $deepgramService->generateText($part_three_result->audio_path);

                    $part_three_result->update([
                        'audio_text'=>$audio_data['text'],
                    ]);

                    }catch (\Throwable $e) {
                        $mime = Storage::disk('private')->mimeType( "voiceAudios/$part_three_result->audio_path") ?? 'application/octet-stream';

                        Log::channel('telegram')->error(
                            "\n\n Deepgram answer transcribe failed, ".$category.
                            "\n result_id ".$speakingResult->id.
                            "\n answer_id ".$part_three_result->id.
                            "\n audio_path ".$part_three_result->audio_path.
                            "\n mime ".$mime.
                            "\n message :  ".$this->escapeForTelegram($e->getMessage())
                        );

                        $part_three_result->update([
                            'audio_text' => " ",
                        ]);

                        sleep(5);
                    }
                }

            }

        }catch (\Exception $exception){
            Log::channel('telegram')->error("\nSpeaking check deepGram error: ".$this->escapeForTelegram($exception->getMessage()));
            Log::channel('telegram2')->error("\nSpeaking check deepGram error: ".$this->escapeForTelegram($exception->getMessage()));
        }

        $speakingResult->update(['status' => 2]);

        DB::beginTransaction();

        try {

            if ( $this->status == 1){
                $partOneFirstResult=$speakingResult->part_one_first;
                $partOneFirstExercise=$speakingResult->exercise->part_one_first;

                $result = $speakingAiService->GeneratePartOneFirstResult($partOneFirstResult,$partOneFirstExercise);
                $allAiPrice=$this->calculateAiCheckPrice($result['result']['usage']);
                $result['result']['result_json']['usd_price']=$allAiPrice['usd_price'];
                $result['result']['result_json']['uzs_price']=$allAiPrice['uzs_price'];

                $partOneFirstResult->update([
                    'score' => floatval($result['result']['result_json']['score']),
                    'result_json'=>$result['result']['result_json'],
                ]);

                $speakingResult->update([
                    'ai_check_price'=>floatval($allAiPrice['usd_price']),
                    'uzs_ai_check_price'=>floatval($allAiPrice['uzs_price']),
                ]);

            }elseif ($this->status==2){
                $partOneSecondResult=$speakingResult->part_one_second;
                $partOneSecondExercise=$speakingResult->exercise->part_one_second;

                $result = $speakingAiService->GeneratePartOneSecondResult($partOneSecondResult,$partOneSecondExercise);
                $allAiPrice=$this->calculateAiCheckPrice($result['result']['usage']);
                $result['result']['result_json']['usd_price']=$allAiPrice['usd_price'];
                $result['result']['result_json']['uzs_price']=$allAiPrice['uzs_price'];

                $partOneSecondResult->update([
                    'score' => floatval($result['result']['result_json']['score']),
                    'result_json'=>$result['result']['result_json'],
                ]);


                if (!$this->type){
                    $speakingResult->update([
                        'status' => 3 ,
                        'process_category_type' =>1,
                        'score' => floatval($speakingResult->part_one_first->score) + floatval($result['result']['result_json']['score']),
                    ]);
                }

                $historyBalance=$speakingResult->ai_check_price;
                $historyUzsBalance=$speakingResult->uzs_ai_check_price;

                $speakingResult->update([
                    'process_category_type' =>1,
                    'ai_check_price'=>floatval($allAiPrice['usd_price']) + floatval($historyBalance),
                    'uzs_ai_check_price'=>floatval($allAiPrice['uzs_price'])+ floatval($historyUzsBalance),
                ]);

            }elseif ($this->status==3){
                $partTwoResult=$speakingResult->part_two;
                $partTwoExercise=$speakingResult->exercise->part_two;

                $result = $speakingAiService->GeneratePartTwoResult($partTwoResult,$partTwoExercise);
                $allAiPrice=$this->calculateAiCheckPrice($result['result']['usage']);
                $result['result']['result_json']['usd_price']=$allAiPrice['usd_price'];
                $result['result']['result_json']['uzs_price']=$allAiPrice['uzs_price'];

                $partTwoResult->update([
                    'score' => floatval($result['result']['result_json']['score']),
                    'result_json'=>$result['result']['result_json'],
                ]);


                if (!$this->type){
                    $speakingResult->update([
                        'status' => 3 ,
                        'process_category_type' =>1,
                        'score' => floatval($result['result']['result_json']['score']),
                    ]);
                }

                $historyBalance=$speakingResult->ai_check_price;
                $historyUzsBalance=$speakingResult->uzs_ai_check_price;
                $speakingResult->update([
                    'process_category_type' =>2,
                    'ai_check_price'=>floatval($allAiPrice['usd_price']) + floatval($historyBalance),
                    'uzs_ai_check_price'=>floatval($allAiPrice['uzs_price']) + floatval($historyUzsBalance),
                ]);

            }elseif ($this->status==4){
                $partThreeResult=$speakingResult->part_three;
                $partThreeExercise=$speakingResult->exercise->part_three;
                $result = $speakingAiService->GeneratePartThreeResult($partThreeResult,$partThreeExercise);
                $allAiPrice=$this->calculateAiCheckPrice($result['result']['usage']);
                $result['result']['result_json']['usd_price']=$allAiPrice['usd_price'];
                $result['result']['result_json']['uzs_price']=$allAiPrice['uzs_price'];

                $partThreeResult->update([
                    'score' => floatval($result['result']['result_json']['score']),
                    'result_json'=>$result['result']['result_json'],
                ]);


                if ($this->type){
                    $all_score=floatval($speakingResult->part_one_first->score ) + floatval($speakingResult->part_one_second->score)+ floatval($speakingResult->part_two->score) + floatval($result['result']['result_json']['score']);
                    $resultOldPrice=$speakingResult->ai_check_price;
                    $resultOldUzsPrice=$speakingResult->uzs_ai_check_price;


                    $speakingResult->update([
                        'status' => 3 ,
                        'process_category_type' =>3,
                        'score' => $all_score,
                        'ai_check_price'=>floatval( $resultOldPrice ) + floatval($allAiPrice['usd_price']),
                        'uzs_ai_check_price'=>floatval( $resultOldUzsPrice ) + floatval($allAiPrice['uzs_price']),
                    ]);
                }else{
                    $speakingResult->update([
                        'status' => 3 ,
                        'process_category_type' =>1,
                        'score' => floatval($result['result']['result_json']['score']),
                        'ai_check_price'=>floatval($allAiPrice['usd_price']),
                        'uzs_ai_check_price'=>floatval($allAiPrice['uzs_price']),
                    ]);
                }

            }

            $price=$speakingResult->ai_check_price;

            if ( ($this->status==2 && !$this->type) || ($this->status==3 && !$this->type) || ($this->status==4 && !$this->type) || ($this->status==4 && $this->type)){

                $this->changeUserBalance($this->userId,$price);

                SpeakingChangeAudioFormat::dispatch($speakingResult->id);

//                Log::channel('telegram')->error(
//                    "\n\n Finish Speaking checking, ".$category.
//                    "\n Result id: ".$this->resultId.
//                    "\n User id: ".$this->userId.
//                    "\n Price: {$price}"
//                );
//                Log::channel('telegram2')->error(
//                    "\n\n Finish Speaking checking, ".$category.
//                    "\n Result id: ".$this->resultId.
//                    "\n User id: ".$this->userId.
//                    "\n Price: {$price}"
//                );
            }

            DB::commit();

        }catch (\Exception $exception){

            DB::rollBack();

            try {
                Log::channel('telegram')->error("\nSpeaking check error: {$this->type}".$this->escapeForTelegram($exception->getMessage()));
                Log::channel('telegram2')->error("\nSpeaking check error:  {$this->type} ".$this->escapeForTelegram($exception->getMessage()));
            } catch (\Throwable $logErr) {
                Log::error("Speaking check error: ".$exception->getMessage(), ['log_error' => $logErr->getMessage()]);
            }

            throw $exception; // muhim: failed_jobs ga tushsin
        }


    }



    public function escapeForTelegram($text)
    {
        $specials = ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'];

        foreach ($specials as $char) {
            $text = str_replace($char, '' . $char, $text);
        }

        return $text;
    }
}
