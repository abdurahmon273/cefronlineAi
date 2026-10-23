<?php

namespace App\Traits;


use App\Enums\CefrTypeEnum;
use App\Models\FullExercise;
use App\Models\ListeningExercise;
use App\Models\PartFourFiveAnswer;
use App\Models\PartOneFiveAnswer;
use App\Models\PartTwoThreeAnswer;
use App\Models\ReadingExercise;
use App\Models\SpeakingExercise;
use App\Models\WritingExercise;

trait PractiseHelperTrait
{
    public function getPractisesData()
    {
        $reading_count = ReadingExercise::whereHas('exercise')->count();

        $listening_count = ListeningExercise::whereHas('exercise')->count();

        $writing_count = WritingExercise::whereHas('exercise')->count();

        $speaking_count = SpeakingExercise::whereHas('exercise')->count();

        $full_count = FullExercise::whereHas('exercise')->count();



        $cards = [
            ['t' => 'Listening', 'sub' => $listening_count, 'grad' => 'bg-[#E03A3A]', 'icon' => 'headphone.svg', 'effect' => 'effects/banner-smoke.svg','type'=>CefrTypeEnum::LISTENING],
            ['t' => 'Reading', 'sub' => $reading_count, 'grad' => 'bg-[#FFB800]', 'icon' => 'book.svg', 'effect' => 'effects/banner-smoke.svg','type'=>CefrTypeEnum::READING],
            ['t' => 'Writing', 'sub' => $writing_count, 'grad' => 'bg-[#2EAE4E]', 'icon' => 'pen.svg', 'effect' => 'effects/banner-smoke.svg','type'=>CefrTypeEnum::WRITING],
            ['t' => 'Speaking', 'sub' => $speaking_count, 'grad' => 'bg-[#FD4D00]', 'icon' => 'mic.svg', 'effect' => 'effects/banner-smoke.svg','type'=>CefrTypeEnum::SPEAKING],
        ];

        $mock_data=['count'=>$full_count];

        return [
            'cards'=>$cards,
            'mock_data'=>$mock_data,
        ];
    }

}
