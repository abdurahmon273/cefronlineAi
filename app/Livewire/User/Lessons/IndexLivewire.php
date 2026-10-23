<?php

namespace App\Livewire\User\Lessons;

use Livewire\Component;
use App\Models\Course;
use App\Models\PremiumToUsers;

class IndexLivewire extends Component
{
    public $courses;

    public function mount()
    {
        $this->courses = \Illuminate\Support\Facades\Cache::remember(
            'user_published_courses',
            now()->addMinutes(5),
            function () {
                return Course::with([
                        'lessons' => function ($q) {
                            $q->select('id', 'course_id', 'name', 'order_id');
                        },
                        'media',
                    ])
                    ->where('published', 1)
                    ->orderBy('order_id')
                    ->select('id', 'name', 'order_id', 'status')
                    ->get();
            }
        );
    }
    public function render()
    {
        return view('livewire.user.lessons.index-livewire');
    }
}
