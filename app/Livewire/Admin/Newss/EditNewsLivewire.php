<?php

namespace App\Livewire\Admin\Newss;

use Livewire\Component;

class EditNewsLivewire extends Component
{
    public $status,$status_type=null,$news;

    public $rules=[
        'status'=>'required'
    ];
    public $messages=[
        'status.required'=>'Status is required',
    ];

    public function mount($news)
    {
        $this->news=$news;
        $this->status=$news->status;
        if($news->status==0){
            $this->dispatch('status_updated',['status_type'=>false]);
        }
        if ($news->status==1){
            $this->dispatch('status_updated',['status_type'=>true]);
        }
    }
    public function updatedStatus($status)
    {

        if($status=='0'){
            $this->status_type=false;
        }elseif ($status=='1'){
            $this->status_type=true;
        }
        if ($status=='null'){
            $this->status_type=null;
        }
        $this->dispatch('status_updated',['status_type'=>$this->status_type,'status'=>$this->status]);
    }
    public function render()
    {
        return view('livewire.admin.newss.edit-news-livewire');
    }
}
