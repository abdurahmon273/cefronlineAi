<?php

namespace App\Livewire\Admin\Full;

use App\Models\Exercise;
use App\Models\FullExercise;
use App\Models\SpeakingExercise;
use App\Models\WritingExercise;
use Livewire\Component;

class FullExerciseLivewire extends Component
{
    public $id;

    public function mount($id)
    {
        $this->id = $id;
    }
    public function updateOrder($data)
    {
        FullExercise::updateOrder($data);
    }
    public function render()
    {

        $fullExercises = FullExercise::where('exercise_id',$this->id)
            ->with('listening_exercise')
            ->orderBy('order_id')
            ->paginate(40);
        $exercise=Exercise::find($this->id);

        return view('livewire.admin.full.full-exercise-livewire',compact('exercise','fullExercises'));
    }
}
