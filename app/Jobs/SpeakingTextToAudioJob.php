<?php

namespace App\Jobs;

use App\Models\SpeakingPartOneFirstQuestions;
use App\Models\SpeakingPartOneSecondQuestions;
use App\Models\SpeakingPartThree;
use App\Models\SpeakingPartThreeQuestions;
use App\Models\SpeakingPartTwoQuestions;
use App\Services\DeepgramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SpeakingTextToAudioJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $type,$id;
    public function __construct($type,$id)
    {
        $this->type = $type;
        $this->id = $id;
    }

    /**
     * Execute the job.
     */
    public function handle(DeepgramService $service): void
    {
        if ($this->type== 'one_one'){
            $part_one_first_question = SpeakingPartOneFirstQuestions::find($this->id);

            $audio_data= $service->generateAudio($part_one_first_question->question,'speaking');

            $part_one_first_question->update([
                'audio_url'=>$audio_data['audio_name'],
            ]);
        }elseif ($this->type== 'one_two'){
            $part_one_second_question = SpeakingPartOneSecondQuestions::find($this->id);
            $audio_data= $service->generateAudio($part_one_second_question->question,'speaking');

            $part_one_second_question->update([
                'audio_url'=>$audio_data['audio_name'],
            ]);
        }elseif ($this->type== 'two'){
            $part_two_question = SpeakingPartTwoQuestions::find($this->id);

            $audio_data= $service->generateAudio($part_two_question->question,'speaking');

            $part_two_question->update([
                'audio_url'=>$audio_data['audio_name'],
            ]);
        }elseif ($this->type== 'three_question'){
            $part_three_question=SpeakingPartThree::find($this->id);

            $audio_data= $service->generateAudio($part_three_question->question_text,'speaking');

            $part_three_question->update([
                'audio_url'=>$audio_data['audio_name'],
            ]);
        }elseif ($this->type== 'three_theme'){
            $part_three_question=SpeakingPartThree::find($this->id);

            $audio_data= $service->generateAudio($part_three_question->theme,'speaking');

            $part_three_question->update([
                'theme_audio_url'=>$audio_data['audio_name'],
            ]);
        }
    }
}
