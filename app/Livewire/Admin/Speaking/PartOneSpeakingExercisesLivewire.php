<?php

namespace App\Livewire\Admin\Speaking;

use App\Models\Exercise;
use App\Models\SpeakingExercise;
use App\Models\WritingExercise;
use Livewire\Component;

class PartOneSpeakingExercisesLivewire extends Component
{
    public $id;

    public function mount($id)
    {
        $this->id = $id;
    }
    public function updateOrder($data)
    {
        SpeakingExercise::updateOrder($data);
    }
    public function render()
    {

        $speakingExercises = SpeakingExercise::where('exercise_id',$this->id)
            ->with('part_one_first.questions','part_one_second.questions')
            ->orderBy('order_id')
            ->paginate(40);
        $exercise=Exercise::find($this->id);

        return view('livewire.admin.speaking.part-one-speaking-exercises-livewire',compact('speakingExercises','exercise'));
    }
}
