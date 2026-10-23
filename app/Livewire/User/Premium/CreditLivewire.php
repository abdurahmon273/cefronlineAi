<?php

namespace App\Livewire\User\Premium;

use App\Models\BalanceEdit;
use App\Models\GlobalSetting;
use App\Models\Package;
use App\Models\Plan;
use App\Models\Promocode;
use App\Traits\BillingTrait;
use Livewire\Component;

class CreditLivewire extends Component
{
    use BillingTrait;

    public $packages = [];
    public $selectedPrice;

    public $globalSetting;
    public $usd_currency,$gpt_tax,$min_price;

    public $package_id;
    public $promoStatus = 0,$promoMessage = '',$appliedCode='',$promocode='',$appliedCodeCode='',$appliedCodeDiscount=0;

    public function mount(): void
    {
        $this->globalSetting = GlobalSetting::orderByDesc('effective_at')->firstOrFail();
        $this->usd_currency = floatval($this->globalSetting->usd_current_currency);
        $this->gpt_tax = $this->globalSetting->chat_gpt_tax;
        $this->min_price = $this->globalSetting->min_ai_credit_price;
        $this->packages=Package::orderBy('order_id')->get()->map(function ($row) {
            $row->discount=0;
            return $row;
        });

    }

    public function applyPromo()
    {
        $code = trim((string) $this->promocode);


        if ($code === '') {
            $this->promoStatus = 1;
            $this->promoMessage = 'Please enter promo code';
            $this->appliedCode = null;
            $this->loadDefaultPackages();
            return;
        }

        $base = Promocode::query()->where('code', $code);

        if (!(clone $base)->exists()) {
            $this->promoStatus = 3;
            $this->promoMessage = 'Promo code not found';
            $this->appliedCode = null;
            $this->loadDefaultPackages();
            return;
        }

        $now = now();

        $dateValid = (clone $base)
            ->where(function ($q) use ($now) {
                $q->where(function ($qq) use ($now) {
                    $qq->where('start_date', '<=', $now)
                        ->where('end_date', '>=', $now);
                });
            })
            ->exists();

        if (!$dateValid) {
            $this->promoStatus = 3;
            $this->promoMessage = 'Promo code is not active';
            $this->appliedCode = null;
            $this->loadDefaultPackages();
            return;
        }

        $onceAlreadyUsed = (clone $base)
            ->where('type', 0)
            ->whereHas('premium_to_users')
            ->exists();

        if ($onceAlreadyUsed) {
            $this->promoStatus = 3;
            $this->promoMessage = 'This promo code has already been used';
            $this->appliedCode = null;
            $this->loadDefaultPackages();
            return;
        }

        $onceAlreadyUsedThisUser = (clone $base)
            ->whereHas('premium_to_users', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->exists();

        if ($onceAlreadyUsedThisUser) {
            $this->promoStatus = 3;
            $this->promoMessage = 'You have already used this promo code';
            $this->appliedCode = null;
            $this->loadDefaultPackages();
            return;
        }

        $promo = (clone $base)
            ->with(['plans:id'])
            ->first(['id', 'code', 'discount']);

        if (!$promo) {
            $this->promoStatus = 3;
            $this->promoMessage = 'Promo code is invalid';
            $this->appliedCode = null;
            $this->loadDefaultPackages();
            return;
        }

        $discount = (float) ($promo->discount ?? 0);
        $promoPackageIds = $promo->packages->pluck('id')->toArray();

        $this->packages=Package::orderBy('order_id')->get()->map(function ($package) use ($promoPackageIds, $discount) {
            $hasDiscount = in_array($package->id, $promoPackageIds);

            $package->discount=$hasDiscount ? $discount : 0;
            $package->discountedPrice=$hasDiscount ? $package->price/100*(100-$discount) : 0;

            return $package;
        });

        $this->promoStatus = 2;
        $this->promoMessage = 'Promo code applied successfully';
        $this->appliedCode = $promo->id;
        $this->appliedCodeCode = $promo->code;
        $this->appliedCodeDiscount = $promo->discount;

        $this->promocode = '';

    }

    public function loadDefaultPackages()
    {
        $this->packages=Package::orderBy('order_id')->get()->map(function ($row) {
            $row->discount=0;
            $row->discountedPrice=0;
            return $row;
        });
    }

    public function buyNow($id): void
    {
        $this->package_id = $id;


        $this->dispatch('open-credit-payment-modal');
    }

    public function purchaseCredit($paymentMethod)
    {
        if (!$this->package_id) return;

        $user = auth()->user();
        $package = Package::findOrFail($this->package_id);
        $price=floatval($package->price);
        $promoCodeId=$this->appliedCode;

        if ($promoCodeId){
            $promoCode=Promocode::with('packages')->find($promoCodeId);
            if ($promoCode->plans->contains('id', $package->id)){
                $price=round($package->price - ( $package->price * ( $promoCode->discount ) / 100 ),2);
            }else{
                $promoCodeId=null;
            }
        }


        $user->update([
            'tmp_payment_slug'=>$package->id,
            'tmp_payment_price'=>$price,
            'tmp_payment_promocode'=>$promoCodeId ?? null,
            'tmp_payment_type'=>1,
        ]);

        $key = auth()->user()->id;
        $payment_url = "https://teacher-muzaffar.com";

        $url = "$payment_url/pay/$paymentMethod/$key/$price?redirect_url=".route('user.profile');


        $this->dispatch('open-payment', url: $url);
    }

    public function activateFreeCredit($id)
    {
        $user = auth()->user();
        $package = Package::findOrFail($id);
        $globalSetting = GlobalSetting::orderByDesc('effective_at')->select('chat_gpt_tax','profit_tax','usd_current_currency')->firstOrFail();
        $discount=100;
        $promoCodeId=$this->appliedCode;

        if ($promoCodeId){
            $promoCode=Promocode::find($promoCodeId);
            $price=round($package->price - ( $package->price * ( $promoCode->discount ) / 100 ),2);
        }



        $premium_user=BalanceEdit::create([
            'user_id' => $user->id,
            'price' => $package->price,
            'value' =>$package->credit_price,
            'current_currency'=>$globalSetting->usd_current_currency,
            'current_tax'=>$globalSetting->chat_gpt_tax+$globalSetting->profit_tax,
            'discount'=>$discount,
            'current_balance'=>$user->balance,
            'type'=>2,
            'package_id'=>$package->id,
            'promocode_id'=>$promoCodeId,
        ]);

        $this->changeUserBalanceByAdmin($user->id,$package->price);

        $this->reset(['promoStatus', 'promoMessage', 'appliedCode', 'appliedCodeCode', 'appliedCodeDiscount', 'promocode', 'package_id']);
        $this->loadDefaultPackages();

        $this->dispatch('close-credit-payment-modal');
        $this->dispatch('balance-updated');
        $this->dispatch('global_alert', 1, 'Free credit activated successfully');
    }
    public function render()
    {
        return view('livewire.user.premium.credit-livewire');
    }
}
