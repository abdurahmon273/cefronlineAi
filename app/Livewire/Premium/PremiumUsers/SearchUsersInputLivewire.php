<?php

namespace App\Livewire\Premium\PremiumUsers;

use App\Models\User;
use Livewire\Component;

class SearchUsersInputLivewire extends Component
{

    public $search,$founded_users=[],$chosen_user;

    public function choose_user($id)
    {

        $user=User::where('id',$id)->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name . ' ' . $user->surname,
                'phone_number' => $user->mobil_number,
            ];
        });
        $this->search=null;
        $this->founded_users=[];
        $this->chosen_user = $user['0'];
        $this->dispatch('choosen_user',$user[0]['id']);
    }

    public function updatedSearch($search)
    {
        $this->founded_users = User::where(function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('surname', 'like', '%' . $this->search . '%')
                ->orWhere('mobil_number', 'like', '%' . $this->search . '%')
                ->orWhere('email', 'like', '%' . $this->search . '%');
        })
            ->limit(20)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name . ' ' . $user->surname,
                    'phone_number' => $user->mobil_number,
                ];
            });
    }


    public function render()
    {
        return view('livewire.premium.premium-users.search-users-input-livewire');
    }
}
