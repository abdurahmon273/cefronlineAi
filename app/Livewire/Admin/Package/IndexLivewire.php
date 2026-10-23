<?php

namespace App\Livewire\Admin\Package;

use App\Models\Package;
use App\Models\Plan;
use Livewire\Component;

class IndexLivewire extends Component
{
    public function updateOrder($data)
    {
        Package::updateOrder($data);
    }

    public function render()
    {
        $packages = Package::orderBy('order_id')->get();

        return view('livewire.admin.package.index-livewire', compact('packages'));
    }
}
