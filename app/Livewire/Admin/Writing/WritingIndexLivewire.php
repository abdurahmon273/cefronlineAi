<?php

namespace App\Livewire\Admin\Writing;

use App\Models\Exercise;
use Livewire\Component;
use Livewire\WithPagination;

class WritingIndexLivewire extends Component
{
//    public $exercises=[];
    use WithPagination;

    protected $paginationTheme = 'bootstrap'; // Optional: Use Bootstrap styling for pagination.



    public function render()
    {

        $exercises = Exercise::with('writing_exercises')->where('category_type', '2')->paginate(20);

        return view('livewire.admin.writing.writing-index-livewire', compact('exercises'));
    }
}
