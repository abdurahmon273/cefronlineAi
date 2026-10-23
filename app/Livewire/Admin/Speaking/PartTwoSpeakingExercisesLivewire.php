<?php

namespace App\Livewire\Admin\Speaking;

use App\Models\Exercise;
use App\Models\SpeakingExercise;
use Livewire\Component;

class PartTwoSpeakingExercisesLivewire extends Component
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
            ->with('part_two')
            ->orderBy('order_id')
            ->paginate(40);
        $exercise=Exercise::find($this->id);

        return view('livewire.admin.speaking.part-two-speaking-exercises-livewire',compact('exercise','speakingExercises'));
    }
}
