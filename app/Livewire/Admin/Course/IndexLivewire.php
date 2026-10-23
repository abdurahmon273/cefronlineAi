<?php

namespace App\Livewire\Admin\Course;

use App\Models\Course;
use Livewire\Component;

class IndexLivewire extends Component
{
    public $courses=[];

    public function mount($courses)
    {
        $this->courses = $courses;
    }
    public function updateOrder($data)
    {
        Course::updateOrder($data);
        $this->courses = Course::with(['media'])->orderBy('order_id')->get();
    }
    public function render()
    {
        return view('livewire.admin.course.index-livewire');
    }
}
