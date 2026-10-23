<?php

namespace App\Livewire\Admin\Lessons;

use App\Models\Video;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class CreateLivewire extends Component
{
    use WithFileUploads;
    public $videos=[],$delete_videos=[],$lesson_id;
    public function mount()
    {
    }

    protected $listeners=[
        'lessonId',
    ];


    public function add_video()
    {
        $this->videos[] = [
            'link' => '',
            'description' => '',
            'files' => [],
        ];
    }

    public function delete_video($key)
    {
        $deleted_price = array_splice($this->videos, $key, 1);
    }

    public function lessonId($lessonId)
    {
        $this->lesson_id=$lessonId;
        $this->store();
    }

    public function updatedVideos($value, $key)
    {
        if (strpos($key, '.files') !== false) {
            $keyParts = explode('.', $key);
            $videoIndex = $keyParts[0];

            // Fayllarni vaqtinchalik saqlash
            if (is_array($value)) {
                $storedFiles = [];
                foreach ($value as $file) {
                    $storedFiles[] = $file->store('temp-uploads', 'public');
                }
                $this->videos[$videoIndex]['files'] = $storedFiles;
            } else {
                $this->videos[$videoIndex]['files'] = $value->store('temp-uploads', 'public');
            }

        }
    }
    public function store()
    {
        foreach ($this->videos as  $video){

            $vi=Video::create([
                'link'=>$video['link'],
                'lesson_id'=>$this['lesson_id'],
                'description'=>$video['description'],

            ]);
        }
        Session::forget('videos'); // Saqlanganidan keyin sessiyani tozalash
        $this->reset();
//        return redirect()->route('admin.lessons.index');

    }

    public function render()
    {
        return view('livewire.admin.lessons.create-livewire');
    }
}
