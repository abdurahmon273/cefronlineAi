<?php

namespace App\Livewire\User\Auth;

use App\Models\StepPage;
use Livewire\Component;

class StepPageLivewire extends Component
{
    public function render()
    {
        $stepPages = StepPage::orderBy('order_id')->get();
        return view('livewire.user.auth.step-page-livewire', compact('stepPages'));
    }
}
