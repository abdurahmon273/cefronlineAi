<?php

namespace App\Livewire\Admin\ListeningPartTwoSixQuestion;

use Livewire\Component;

class IndexLivewire extends Component
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
        return view('livewire.admin.listening-part-two-six-question.index-livewire');
    }
}
