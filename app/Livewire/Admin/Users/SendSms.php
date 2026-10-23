<?php

namespace App\Livewire\Admin\Users;

use App\Models\Message;
use App\Models\Permission;
use App\Models\Shablon;
use Livewire\Component;
use App\Traits\SendingSms;

use function Laravel\Prompts\error;

class SendSms extends Component
{
    use SendingSms;
    public $shablons=[], $description = '', $shablonId, $users_id,$b=false;

    protected $listeners = [
        'users_id' => 'send_user'
    ];
    public $rules = [
        'shablonId' => 'required',
    ];
    public $messages = [
        'shablonId.required' => 'Shablon tanlanmadi'
    ];

    public function mount()
    {
        $this->shablons = Shablon::where('status','done')->pluck('name', 'id');
    }

    public function send_user($ids)
    {
        $this->users_id = $ids;
    }

    public function updatedShablonId($value)
    {
        if ($value !== '') {
            $sh = Shablon::where('id', $value)->first();
            $this->description = $sh->description;
            $this->clearValidation();
        } else {
            $this->description = '';
        }
    }

    public function send()
    {
        $this->validate();
        if ($this->users_id !== []) {
            $mobil_numbers=[];
//            $message=[];
            foreach ($this->users_id as $id) {
                $m=Message::create([
                    'message' => $this->description,
                    'user_id' => $id,
                ]);
                $int_var = preg_replace('/[^0-9]/', '', $m->user->mobil_number);
                $mobil_numbers[]=$int_var;
            }
            $message=$this->description;

                $this->send_sms($mobil_numbers,$message);
                $this->dispatch('render');
                $this->js("alert('sms yuborildi')");
            $this->reset();
            $this->clearValidation();
        }else{
            $this->b=true;
        }
    }

    public function clear()
    {
        $this->b=false;
        $this->reset('description','shablonId');
        $this->clearValidation();
    }
    public function render()
    {
        return view('livewire.admin.users.send-sms');
    }
}
