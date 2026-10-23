<?php

namespace App\Traits;

use App\Http\Resources\Reading\ReadingExerciseWithPartResource;
use App\Models\PrepareText;
use App\Models\ReadingExercise;

trait TestDataTrait
{
    public function GetReadingData($id)
    {
        $reading_exercise=ReadingExercise::find($id);

        return new ReadingExerciseWithPartResource($reading_exercise);
    }
}
