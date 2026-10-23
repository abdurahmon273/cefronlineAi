<?php

namespace App\Jobs;

use App\Enums\CefrTypeEnum;
use App\Models\User;
use App\Models\WritingResult;
use App\Services\ChatGPTService;
use App\Services\WritingAiService;
use App\Traits\BillingTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WritingCheck implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels,BillingTrait;

    public int $resultId;
    public int $userId;
    public int $status , $type;
    public function __construct($resultId, $userId,$status, $type)
    {
        $this->resultId = $resultId;
        $this->userId = $userId;
        $this->status = $status;
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle(WritingAiService $service,ChatGPTService $chatGPTService): void
    {
        $writingResult = WritingResult::with(['partOneAnswer','partTwoAnswer','exercise.task_one','exercise.task_two'])
            ->findOrFail($this->resultId);

        if (!$writingResult){
            return;
        }

        if (!$this->type && $this->status==3){
            $writingResult->update([
                'status' => 2 ,
            ]);
        }

        DB::beginTransaction();

        try {

            $category=WritingResult::CATEGORY_TYPES[$writingResult->category_type];

            if ( ($this->status==1 && $this->type) || ($this->status==1 && !$this->type) ||  ($this->status==3 && !$this->type)  ){
//                Log::channel('telegram')->error(
//                    "\n\n Start Writing Checking, ".$category.
//                    "\n Result id ".$this->resultId.
//                    "\n User id ".$this->userId
//                );

            }



            if ($this->status==1){
                $question=$writingResult->exercise->task_one->theme;
                $text=$writingResult->partOneAnswer->text_1;

                $result = $service->GeneratePartOneFirstResult($question,$text);
                $allAiPrice=$this->calculateAiCheckPrice($result['result']['usage']);
                $result['result']['result_json']['price']=$allAiPrice['usd_price'];
                $result['result']['result_json']['uzs_price']=$allAiPrice['uzs_price'];

                $writingResult->partOneAnswer->update([
                    'score_1' => floatval($result['result']['result_json']['score']),
                    'max_score' => CefrTypeEnum::WRITING_MAX_SCORES[1],
                    'result_1_json'=>$result['result']['result_json'],
                ]);

                $writingResult->update([
                    'ai_check_price'=>floatval($allAiPrice['usd_price']),
                    'uzs_ai_check_price'=>floatval($allAiPrice['uzs_price']),
                ]);

                $writingResult->update([
                    'status'=>2
                ]);

            }
            elseif ($this->status==2){

                $question=$writingResult->exercise->task_one->theme;
                $text=$writingResult->partOneAnswer->text_2;

                $result = $service->GeneratePartOneSecondResult($question,$text);
                $allAiPrice=$this->calculateAiCheckPrice($result['result']['usage']);
                $result['result']['result_json']['price']=$allAiPrice['usd_price'];
                $result['result']['result_json']['uzs_price']=$allAiPrice['uzs_price'];

                $result_1=json_decode($writingResult->partOneAnswer->result_1_json,true);

                $writingResult->partOneAnswer->update([
                    'score_2' => floatval($result['result']['result_json']['score']),
                    'result_2_json'=>$result['result']['result_json'],
                ]);

                if (!$this->type){
                    $writingResult->update([
                        'status' => 3 ,
                        'process_category_type'=>1,
                        'score' => floatval($result_1['score']) + floatval($result['result']['result_json']['score']),
                    ]);
                }else{
                    $writingResult->update([
                        'status' => 2 ,
                        'process_category_type'=>1,
                    ]);
                }

                $resultOldPrice=$writingResult->ai_check_price;
                $resultOldUZSPrice=$writingResult->uzs_ai_check_price;
                $writingResult->update([
                    'ai_check_price'=>floatval( $allAiPrice['usd_price'] ) + floatval($resultOldPrice ),
                    'uzs_ai_check_price' => floatval( $allAiPrice['uzs_price'] ) + floatval($resultOldUZSPrice )
                ]);



            }
            elseif ($this->status==3){

                $question=$writingResult->exercise->task_two->theme;
                $text=$writingResult->partTwoAnswer->text;

                $result = $service->GeneratePartTwoResult($question,$text);
                $allAiPrice=$this->calculateAiCheckPrice($result['usage']);
                $result['result_json']['price']=$allAiPrice['usd_price'];
                $result['result_json']['uzs_price']=$allAiPrice['uzs_price'];

                $writingResult->partTwoAnswer->update([
                    'score' => floatval($result['result_json']['score']),
                    'max_score' => CefrTypeEnum::WRITING_MAX_SCORES[2],
                    'result_json'=>$result['result_json'],
                ]);


                if ($this->type){
                   $all_score=floatval($writingResult->partOneAnswer->score_1 ) + floatval($writingResult->partOneAnswer->score_2) + floatval($result['result_json']['score']);
                   $ball=CefrTypeEnum::WRITING_BALL[3][$all_score];
                   $resultOldPrice=$writingResult->ai_check_price;
                   $resultOldUZSPrice=$writingResult->uzs_ai_check_price;


                    $writingResult->update([
                        'status' => 3 ,
                        'process_category_type'=>2,
                        'score' => $all_score,
                        'ai_check_price'=>floatval( $resultOldPrice ) + floatval($allAiPrice['usd_price']),
                        'uzs_ai_check_price'=>floatval( $resultOldUZSPrice ) + floatval($allAiPrice['uzs_price']),
                        'ball'=>$ball,
                        'degree'=>$service->generateDegree($ball),
                    ]);

                }else{
                    $writingResult->update([
                        'status' => 3 ,
                        'process_category_type'=>1,
                        'score' => floatval($result['result_json']['score']),
                        'ai_check_price'=> floatval($allAiPrice['usd_price']),
                        'uzs_ai_check_price'=> floatval($allAiPrice['uzs_price']),
                    ]);
                }


            }



            $price=$writingResult->ai_check_price;
            $uzs_price=$writingResult->uzs_ai_check_price;

        if ( ($this->status==2 && !$this->type) || ($this->status==3 && !$this->type) || ($this->status==3 && $this->type)){

            $this->changeUserBalance($this->userId,$price);

//            Log::channel('telegram')->error(
//                "\n\n Finish Writing checking, ".$category.
//                "\n Result id: ".$this->resultId.
//                "\n User id: ".$this->userId.
//                "\n Price: {$price}".
//                "\n Price: {$uzs_price}"
//            );

        }

            DB::commit();
        }catch (\Exception $exception){

            DB::rollBack();

            try {
                Log::channel('telegram')->error("\nWriting check error: ".$this->escapeForTelegram($exception->getMessage()));
                Log::channel('telegram2')->error("\nWriting check error: ".$this->escapeForTelegram($exception->getMessage()));
            } catch (\Throwable $logErr) {
                Log::error("Writing check error: ".$exception->getMessage(), ['log_error' => $logErr->getMessage()]);
            }

            throw $exception; // muhim: failed_jobs ga tushsin
        }
    }

   public function escapeForTelegram($text)
    {
        $specials = ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'];

        foreach ($specials as $char) {
            $text = str_replace($char, ' ' . $char, $text);
        }

        return $text;
    }
}
