<?php

namespace App\Livewire\Admin\StepPage;

use App\Models\Exercise;
use App\Models\StepPage;
use Livewire\Component;

class IndexLivewire extends Component
{
    public $stepPages=[];

    public function mount($stepPages)
    {
        $this->stepPages = $stepPages;
    }
    public function updateOrder($data)
    {
        StepPage::updateOrder($data);
        $this->stepPages=  StepPage::orderBy('order_id')->get();
    }
    public function render()
    {
        return view('livewire.admin.step-page.index-livewire');
    }
}
