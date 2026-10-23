<?php

namespace App\Livewire\Admin;

use App\Models\Exercise;
use Livewire\Component;

class ReadingIndexLivewire extends Component
{
    public $exercises=[];

    public function mount($exercises)
    {
        $this->exercises = $exercises;
    }

    public function updateOrder($data)
    {
        Exercise::updateOrder($data);
        $this->exercises=  Exercise::with('reading_exercises')->where('category_type','1')->orderBy('order_id')->get();
    }
    public function render()
    {
        return view('livewire.admin.reading-index-livewire');
    }
}
