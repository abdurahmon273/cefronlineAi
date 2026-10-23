<?php

namespace App\Livewire\Admin\ListeningExercises;

use App\Models\Lesson;
use App\Models\ListeningExercise;
use Livewire\Component;

class IndexLivewire extends Component
{
    public $listeningExercises=[];

    public $exercise_id;

    public function updateOrder($data)
    {
        ListeningExercise::updateOrder($data);
        $this->listeningExercises=  ListeningExercise::with(['part_one', 'part_two'])->where('exercise_id',$this->exercise_id)->orderBy('order_id')->get();
    }
    public function mount($listenings,$id,$exercise)
    {
        $this->listeningExercises=$listenings;
        $this->exercise_id=$id;
    }
    public function render()
    {
        return view('livewire.admin.listening-exercises.index-livewire');
    }
}
