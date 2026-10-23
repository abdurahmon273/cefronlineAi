<?php

namespace App\Livewire\Videos;

use App\Models\Lesson;
use App\Models\Video;
use Livewire\Component;

class IndexLivewire extends Component
{
    public $videos=[],$id;
    public function mount($videos,$id)
    {
        $this->videos=$videos;
        $this->id=$id;
    }
    public function updateOrder($data)
    {
        Video::updateOrder($data);
        $this->videos=  Video::with('media')->where('lesson_id',$this->id)->orderBy('order_id')->get();
    }
    public function render()
    {
        return view('livewire.videos.index-livewire');
    }
}
