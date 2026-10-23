<?php

namespace App\Livewire\Admin\Reading;

use Livewire\Component;

class PartThreeIndexLivewire extends Component
{

    public  $readingExercise;

    public function mount($reading_exercise)
    {
        $this->readingExercise = $reading_exercise;
    }

    public function render()
    {
        return view('livewire.admin.reading.part-three-index-livewire');
    }
}
