<?php

namespace App\Livewire\Admin\Reading;

use App\Models\ListeningExercise;
use App\Models\ReadingExercise;
use Livewire\Component;

class ReadingExerciseIndexLivewire extends Component
{
    public $readingExercises=[],$exercise_id;

    public function mount($readings,$id)
    {
        $this->readingExercises = $readings;
        $this->exercise_id=$id;
    }
    public function updateOrder($data)
    {
        ReadingExercise::updateOrder($data);
        $this->readingExercises=  ReadingExercise::where('exercise_id',$this->exercise_id)->orderBy('order_id')->get();
    }
    public function render()
    {
        return view('livewire.admin.reading.reading-exercise-index-livewire');
    }
}
