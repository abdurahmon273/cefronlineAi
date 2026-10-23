<?php

namespace App\Livewire\Admin\Reading;

use Livewire\Component;

class PartFourIndexLivewire extends Component
{
    public $reading_exercise;
    public $part_four,$tfn_tests,$mchq_tests;

    public function mount($reading_exercise,$part_four,$tfn_tests,$mchq_tests)
    {
        $this->reading_exercise=$reading_exercise;
        $this->part_four=$part_four;
        $this->tfn_tests=$tfn_tests;
        $this->mchq_tests=$mchq_tests;
    }
    public function render()
    {
        return view('livewire.admin.reading.part-four-index-livewire');
    }
}
