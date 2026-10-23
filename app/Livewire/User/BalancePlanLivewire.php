<?php

namespace App\Livewire\User;

use Livewire\Attributes\On;
use Livewire\Component;

class BalancePlanLivewire extends Component
{
    public function render()
    {
        $user = auth()->user();

        $balanceData = $user ? $user->balanceData() : ['success' => false];
        $planName = $user ? optional(optional($user->activePremium())->plan)->name : '';

        return view('livewire.user.balance-plan-livewire', [
            'balanceData' => $balanceData,
            'planName' => $planName,
        ]);
    }

    #[On('balance-updated')]
    public function refreshBalance(): void
    {
        //
    }
}
