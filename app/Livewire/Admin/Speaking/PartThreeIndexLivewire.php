<?php

namespace App\Livewire\Admin\Speaking;

use Livewire\Component;

class PartThreeIndexLivewire extends Component
{
    public  $speaking_exercise;

    public function mount($speaking_exercise)
    {
        $this->speaking_exercise = $speaking_exercise;
    }

    public function render()
    {
        return view('livewire.admin.speaking.part-three-index-livewire');
    }
}
