<?php

namespace App\Livewire\Admin\Plan;

use App\Models\Plan;
use App\Models\StepPage;
use Livewire\Component;

class IndexLivewire extends Component
{
    public function updateOrder($data)
    {
        Plan::updateOrder($data);
    }

    public function render()
    {
        $plans = Plan::orderBy('order_id')->get();

        return view('livewire.admin.plan.index-livewire', ['plans' => $plans]);
    }
}
