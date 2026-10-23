<?php

namespace App\Livewire\Admin\Speaking;

use App\Models\Exercise;
use App\Models\SpeakingExercise;
use Livewire\Component;

class FullSpeakingExercisesLivewire extends Component
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

        $speakingExercises = SpeakingExercise::where('exercise_id', $this->id)->with('part_one_first.questions','part_one_second.questions','part_one_second.images','part_two.images','part_two.questions','part_three.questions')
            ->whereHas('part_three')
            ->whereHas('part_one_first')
            ->whereHas('part_one_second')
            ->whereHas('part_two')
            ->with(['part_three', 'part_one_first', 'part_one_second', 'part_two'])
            ->orderBy('order_id')
            ->paginate(40);
        $exercise=Exercise::find($this->id);

        return view('livewire.admin.speaking.full-speaking-exercises-livewire',compact('exercise','speakingExercises'));
    }
}
