<?php

namespace App\Livewire\User;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\SavedLesson;

class FavoriteLivewire extends Component
{
    public $savedLessons;

    public function mount()
    {
        $this->savedLessons = SavedLesson::with(['video'])->where('user_id',Auth::id())->where('saved',1)->orderByDesc('id')->get();
    }

    public function render()
    {
        return view('livewire.user.favorite-livewire');
    }
}
