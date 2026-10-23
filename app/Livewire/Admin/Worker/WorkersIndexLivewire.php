<?php

namespace App\Livewire\Admin\Worker;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class WorkersIndexLivewire extends Component
{
    use WithPagination;

    public $perPage = 20; // Define items per page
    public $search = ''; // Search input value
    protected $paginationTheme = 'bootstrap'; // Use Bootstrap pagination theme

    protected $listeners=[
        'search_users'
    ];
    public function updatingSearch()
    {
        $this->resetPage(); // Reset pagination when search changes
    }

    public function search_users($search)
    {
        $this->search=$search;
        $this->render();
    }

    public function render()
    {
        $users = User::
        with('roles', 'city.region')
            ->whereHas('roles', function ($query) {
                $query->where('title', 'worker');
            })
            ->when($this->search, function ($query) {
                $query->where(function ($subQuery){
                    $subQuery->where('name', 'like', "%{$this->search}%")
                        ->orWhere('surname', 'like', "%{$this->search}%");
                });
            })
            ->paginate($this->perPage);


        return view('livewire.admin.worker.workers-index-livewire', compact('users'));
    }
}
