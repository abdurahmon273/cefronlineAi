<?php

namespace App\Livewire\Admin\Full;

use App\Models\Exercise;
use Livewire\Component;

class FullIndexLivewire extends Component
{

    public function updateOrder($data)
    {
        Exercise::updateOrder($data);
    }
    public function render()
    {
        $exercises = Exercise::with('listening_exercise')->where('category_type','4')->orderBy('order_id')->get();

        return view('livewire.admin.full.full-index-livewire',compact('exercises'));
    }
}
