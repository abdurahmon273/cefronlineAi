<?php

namespace App\Livewire\Admin\Listening;

use Livewire\Component;

class ListeningPartFourIndexLivewire extends Component
{
    public $listeningExercise;

    public $text;

    public function mount($listeningExercise,$text)
    {
        $this->listeningExercise = $listeningExercise;
        $this->text = $text;
    }

    public function render()
    {
        return view('livewire.admin.listening.listening-part-four-index-livewire');
    }
}
