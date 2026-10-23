<?php

namespace App\Livewire\Admin\Writing;

use App\Models\Exercise;
use App\Models\WritingExercise;
use Livewire\Component;

class FullWritingExerciseIndexLivewire extends Component
{
    public $id;


    public function mount($id)
    {
        $this->id = $id;
    }
    public function updateOrder($data)
    {
        WritingExercise::updateOrder($data);
    }
    public function render()
    {

        $writingExercises = WritingExercise::with('task_one','task_two')->where('exercise_id',$this->id)
            ->with('task_two','task_one')
            ->orderBy('order_id')
            ->paginate(40);
        $exercise=Exercise::find($this->id);

        return view('livewire.admin.writing.full-writing-exercise-index-livewire',compact('writingExercises','exercise'));
    }
}
