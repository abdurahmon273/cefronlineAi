<?php

namespace App\Livewire\Admin\ListeningPartThreeFour;

use Livewire\Component;

class FourIndexLivewire extends Component
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
        return view('livewire.admin.listening-part-three-four.four-index-livewire');
    }
}
