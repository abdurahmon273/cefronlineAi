<?php

namespace App\Services;

use App\Enums\CefrTypeEnum;
use App\Jobs\WritingCheck;
use App\Models\WritingPrompt;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WritingStoreService
{

    public function __construct()
    {

    }

    public function dispatchToJob($writingResult,$writingExercise,$user_id)
    {
        $partCategory = (int) $writingExercise->part_category;

        // Full: task1 (1 -> 2) keyin task2 (3) — hammasi ketma-ket
        if ($partCategory === 3) {
            Bus::chain([
                new WritingCheck($writingResult->id, $user_id, 1, 1),
                new WritingCheck($writingResult->id, $user_id, 2, 1),
                new WritingCheck($writingResult->id, $user_id, 3, 1),
            ])->onQueue('writing')->dispatch();

            return;
        }

        // Task 1 only: status 1 keyin 2 (2 status 1 ning result_1_json iga bog'liq)
        if ($partCategory === 1) {
            Bus::chain([
                new WritingCheck($writingResult->id, $user_id, 1, 0),
                new WritingCheck($writingResult->id, $user_id, 2, 0),
            ])->onQueue('writing')->dispatch();

            return;
        }

        // Task 2 only: mustaqil, bitta job
        if ($partCategory === 2) {
            WritingCheck::dispatch($writingResult->id, $user_id, 3, 0)
                ->onQueue('writing');

            return;
        }
    }

}
