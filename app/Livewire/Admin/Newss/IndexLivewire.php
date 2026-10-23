<?php

namespace App\Livewire\Admin\Newss;

use App\Models\Exercise;
use App\Models\News;
use Livewire\Component;

class IndexLivewire extends Component
{

    public function updateOrder($data)
    {
        News::updateOrder($data);
    }

    private function extractVideoId($url)
    {
        $pattern = '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i';
        preg_match($pattern, $url, $matches);

        return $matches[1] ?? null;
    }

    public function render()
    {

        $newss = News::with(['media'])->orderBy('order_id')->get();

        foreach ($newss as $news) {
            $news->video_link = $this->extractVideoId($news->video_link);
        }

        return view('livewire.admin.newss.index-livewire',compact('newss'));
    }
}
