<?php

namespace App\Livewire\Admin\Writing;

use App\Models\Exercise;
use App\Models\ReadingExercise;
use App\Models\WritingExercise;
use Livewire\Component;
use Livewire\WithPagination;

class TaskOneWritingExerciseIndexLivewire extends Component
{
    use WithPagination;

    public $id;

    public function mount($id)
    {
        $this->id = $id;
    }

    public function updateOrder($data)
    {
        // Update the order in the database
        WritingExercise::updateOrder($data);

        // No need to set paginated data to a property, just refresh the view
    }

    public function render()
    {
        $writingExercises = WritingExercise::where('exercise_id', $this->id)
            ->with('task_one')
            ->orderBy('order_id')
            ->paginate(40);

        $exercise = Exercise::find($this->id);

        return view('livewire.admin.writing.task-one-writing-exercise-index-livewire', compact('writingExercises', 'exercise'));
    }
}
