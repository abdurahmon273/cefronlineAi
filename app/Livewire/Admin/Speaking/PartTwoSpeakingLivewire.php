<?php

namespace App\Livewire\Admin\Speaking;

use App\Models\Exercise;
use Livewire\Component;
use Livewire\WithPagination;

class PartTwoSpeakingLivewire extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap'; // Optional: Use Bootstrap styling for pagination.


    public function updateOrder($data)
    {
        Exercise::updateOrder($data);
    }

    public function render()
    {

        $exercises = Exercise::with('writing_exercises')->where('category_type', '3')->where('part_type', '2')->orderBy('order_id')->paginate(20);

        return view('livewire.admin.speaking.part-two-speaking-livewire', compact('exercises'));
    }
}
