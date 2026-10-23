<?php

namespace App\Livewire\User\Lessons;

use Livewire\Component;
use App\Models\Lesson;

class DetailLivewire extends Component
{
    public $lessons;

    public function mount($id)
    {
        $this->lessons = Lesson::with(['course'])
                    ->where('course_id', $id)
                    ->orderBy('order_id')
                    ->select('id', 'course_id', 'name', 'order_id')
                    ->get();
    }
    public function render()
    {
        return view('livewire.user.lessons.detail-livewire');
    }
}
