<?php

namespace App\Livewire\Admin\Dashboard;

use Livewire\Component;

class ViewChartLivewire extends Component
{
    public $FilterMonth, $FilterYear;

    public function mount($FilterMonth, $FilterYear)
    {
        $this->FilterMonth = $FilterMonth;
        $this->FilterYear = $FilterYear;

        $this->dispatch('ReloadChart',[[1,2,3,4,5],[111,122,133,144,55]]);
    }

    public function render()
    {
        return view('livewire.admin.dashboard.view-chart-livewire');
    }
}
