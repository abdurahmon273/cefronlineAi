<?php

namespace App\Livewire\Admin\Listening;

use App\Models\Course;
use App\Models\Exercise;
use App\Models\ListeningExercise;
use Livewire\Component;

class IndexLivewire extends Component
{
    public $exercises=[];

    public function mount($exercises)
    {
        $this->exercises = $exercises;
    }
    public function updateOrder($data)
    {
        Exercise::updateOrder($data);
        $this->exercises=  Exercise::with('listening_exercises')->where('category_type','0')->orderBy('order_id')->get();
    }
    public function render()
    {
        return view('livewire.admin.listening.index-livewire');
    }
}
