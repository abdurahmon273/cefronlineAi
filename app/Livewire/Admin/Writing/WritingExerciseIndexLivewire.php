<?php

namespace App\Livewire\Admin\Writing;

use Livewire\Component;

class WritingExerciseIndexLivewire extends Component
{
    public $writingExercises=[];

    public function mount($readings)
    {
        $this->writingExercises = $readings;
    }

    public function render()
    {
        return view('livewire.admin.writing.writing-exercise-index-livewire');
    }
}
