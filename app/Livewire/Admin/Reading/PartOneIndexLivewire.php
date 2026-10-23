<?php

namespace App\Livewire\Admin\Reading;

use Livewire\Component;

class PartOneIndexLivewire extends Component
{
    public  $readingExercise;
    public $text;

    public function mount($reading_exercise,$text)
    {
        $this->text = $text;
       $this->readingExercise = $reading_exercise;
    }

    public function render()
    {
        return view('livewire.admin.reading.part-one-index-livewire');
    }
}
