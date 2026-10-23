<?php

namespace App\Livewire\Admin\Lessons;

use App\Models\Lesson;
use Livewire\Component;

class IndexLivewire extends Component
{
    public $lessons=[],$id;

    public function mount($lessons,$id)
    {
        $this->lessons = $lessons;
        $this->id = $id;
//        $this->lessons = Lesson::with(['course', 'media'])->where('course_id',$id)->get();
    }
    public function updateOrder($data)
    {
        Lesson::updateOrder($data);
      $this->lessons=  Lesson::with(['course', 'media'])->where('course_id',$this->id)->orderBy('order_id')->get();
    }
    public function render()
    {
        return view('livewire.admin.lessons.index-livewire');
    }
}
