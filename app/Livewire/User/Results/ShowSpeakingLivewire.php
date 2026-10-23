<?php

namespace App\Livewire\User\Results;

use Livewire\Component;

class ShowSpeakingLivewire extends Component
{
    public array $data = [];
    public $type=1;

    public function mount(array $data,$type=1): void
    {
        $this->data = $data;
        $this->type = $type;
    }
    public function render()
    {
        return view('livewire.user.results.show-speaking-livewire',$this->data);
    }
}
