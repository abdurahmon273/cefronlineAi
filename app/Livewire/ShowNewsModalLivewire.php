<?php

namespace App\Livewire;

use Livewire\Component;

class ShowNewsModalLivewire extends Component
{
    public $showNewsModal = false;
    public $selectedNews = null;

    protected $listeners = ['open-news-modal' => 'openModal'];

    public function openModal($notification)
    {
        $this->selectedNews = [
            'title' => $notification['title'],
            'description' => $notification['text'],
            'web_image' => ['url' => $notification['web_image']['original_url'] ?? null],
            'image' => ['url' => $notification['image']['original_url'] ?? null],
            'link' => $notification['link'] ?? null,
        ];
        $this->showNewsModal = true;
    }

    public function closeModal()
    {
        $this->showNewsModal = false;
        $this->selectedNews = null;
    }

    public function render()
    {
        return view('livewire.show-news-modal-livewire');
    }
}
