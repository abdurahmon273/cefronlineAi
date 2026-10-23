<?php

namespace App\Services;

use App\Enums\CefrTypeEnum;
use App\Jobs\CheckSpeaking;
use App\Jobs\WritingCheck;
use App\Models\WritingPrompt;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SpeakingStoreService
{

    public function __construct()
    {

    }

    public function historydDispatchToJob($speakingResult,$speakingExercise,$user_id){

        if ($speakingExercise->part_category==1 || $speakingExercise->part_category==4){
            $type=$speakingExercise->part_category==4 ? 1 : 0;

            CheckSpeaking::dispatch($speakingResult->id,$user_id,1,$type)->onQueue('speaking');

            CheckSpeaking::dispatch($speakingResult->id,$user_id,2,$type)->onQueue('speaking');

        }

        if ($speakingExercise->part_category==2 || $speakingExercise->part_category==4){

            $type=$speakingExercise->part_category==4 ? 1 : 0;

            CheckSpeaking::dispatch($speakingResult->id,$user_id,3,$type)->onQueue('speaking');

        }

        if ($speakingExercise->part_category==3 || $speakingExercise->part_category==4){

            $type=$speakingExercise->part_category==4 ? 1 : 0;

            CheckSpeaking::dispatch($speakingResult->id,$user_id,4,$type)->onQueue('speaking');

        }
    }
    public function dispatchToJob($speakingResult,$speakingExercise,$user_id)
    {
        $partCategory = (int) $speakingExercise->part_category;

        if ($partCategory === 4) {
            Bus::chain([
                new CheckSpeaking($speakingResult->id, $user_id, 1, 1),
                new CheckSpeaking($speakingResult->id, $user_id, 2, 1),
                new CheckSpeaking($speakingResult->id, $user_id, 3, 1),
                new CheckSpeaking($speakingResult->id, $user_id, 4, 1),
            ])->onQueue('speaking')->dispatch();

            return;
        }

        if ($partCategory === 1) {
            Bus::chain([
                new CheckSpeaking($speakingResult->id, $user_id, 1, 0),
                new CheckSpeaking($speakingResult->id, $user_id, 2, 0),
            ])->onQueue('speaking')->dispatch();

            return;
        }

        if ($partCategory === 2) {
            CheckSpeaking::dispatch($speakingResult->id, $user_id, 3, 0)
                ->onQueue('speaking');

            return;
        }

        if ($partCategory === 3) {
            CheckSpeaking::dispatch($speakingResult->id, $user_id, 4, 0)
                ->onQueue('speaking');

            return;
        }



    }

}
