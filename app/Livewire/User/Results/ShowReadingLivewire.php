<?php

namespace App\Livewire\User\Results;

use Livewire\Component;

class ShowReadingLivewire extends Component
{
    public array $data = [];
    public $type=0;

    public function mount(array $data,$type=1): void
    {
        $this->data = $data;
        $this->type = $type;
    }

    public function render()
    {
        return view('livewire.user.results.show-reading-livewire',$this->data);
    }
}
