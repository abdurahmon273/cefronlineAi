<?php

namespace App\Livewire\User;

use App\Traits\PractiseHelperTrait;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\SavedLesson;
use App\Models\Course;
use App\Models\News;

class HomeLivewire extends Component
{
    use PractiseHelperTrait;
    public $savedLessons;
    public $courses;
    public $cards=[],$mock_data=[];
    public $news;
    public $slideCount;

    public function mount()
    {
        $this->savedLessons = SavedLesson::with(['video'])->where('user_id',Auth::id())->where('saved',1)->orderByDesc('id')->take(4)->get();
        $this->news = News::orderBy('order_id')->get();
        $this->courses = Course::with(['lessons'])
            ->where('published',1)
            ->orderBy('order_id')
            ->take(4)
            ->get();
        $data=$this->getPractisesData();
        $this->cards=$data['cards'];
        $this->mock_data=$data['mock_data'];

        $this->slideCount = $this->news->count();
    }
    public function render()
    {
        return view('livewire.user.home-livewire');
    }
}
