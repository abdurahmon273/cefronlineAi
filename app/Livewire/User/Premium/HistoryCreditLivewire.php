<?php

namespace App\Livewire\User\Premium;

use Livewire\Component;

class HistoryCreditLivewire extends Component
{

    public $price,$value;

    public $globalSetting;
    public $usd_currency,$gpt_tax,$min_price;

    public $defaultPriceData=[];
    public function mount(): void
    {
        $this->globalSetting=GlobalSetting::orderByDesc('effective_at')->firstOrFail();
        $this->usd_currency=floatval($this->globalSetting->usd_current_currency);
        $this->gpt_tax=$this->globalSetting->chat_gpt_tax;
        $this->min_price=$this->globalSetting->min_ai_credit_price;
        $this->value=$this->globalSetting->default_price;
        $this->defaultPriceData = [0.09, 0.15, 0.27, 0.30, 0.60, 0.90, 3, 6, 9];
    }

    public function updatedPrice($value)
    {
       $this->value=(floatval($value) * $this->usd_currency) * ($this->gpt_tax+100+50) / 100;
    }

    public function buyNow(): void
    {
        $this->validate([
            'price' => ['required', 'numeric', 'min:' . $this->min_price],
        ], [
            'price.required' => 'Price is required.',
            'price.numeric'  => 'Price must be a valid number.',
            'price.min'      => 'Minimum price is ' . $this->min_price . '.',
        ]);

        dd($this->price,$this->usd_currency,$this->gpt_tax,$this->min_price,$this->defaultPriceData);
    }
    public function render()
    {
        return view('livewire.user.premium.history-credit-livewire');
    }
}
