<?php

namespace App\Livewire\Admin\Writing;

use App\Models\Exercise;
use Livewire\Component;
use Livewire\WithPagination;

class TaskFullWritingIndexLivewire extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap'; // Optional: Use Bootstrap styling for pagination.


    public function updateOrder($data)
    {
        Exercise::updateOrder($data);
    }


    public function render()
    {

        $exercises = Exercise::with('writing_exercises')->where('category_type', '2')->where('part_type', '3')->orderBy('order_id')->paginate(20);

        return view('livewire.admin.writing.task-full-writing-index-livewire',compact('exercises'));
    }
}
