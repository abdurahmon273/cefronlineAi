<?php

namespace App\Livewire\Admin\ListeningPartFiveQuestion;

use Livewire\Component;

class IndexLivewire extends Component
{
    public $tests=[];
    public $question_id;

    public function mount($tests,$question_id)
    {
        $this->tests=$tests;
        $this->question_id=$question_id;
    }

    public function render()
    {
        return view('livewire.admin.listening-part-five-question.index-livewire');
    }
}
