<?php

namespace App\Livewire\User\Premium;

use App\Models\Facility;
use App\Models\Plan;
use App\Models\PremiumToUsers;
use App\Models\Promocode;
use App\Models\User;
use App\Traits\BillingTrait;
use Carbon\Carbon;
use Livewire\Component;

class SubscriptionLivewire extends Component
{
    use BillingTrait;

    public $plans = [];
    public $facilities = [];

    public $promoStatus = 0;
    public $promocode = '';
    public $appliedCode = '';
    public $promoMessage = '';
    public $appliedCodeName = '',$appliedCodeDiscount = '';

    public $selectedPlanId = null;

    public function mount()
    {
        $this->facilities = Facility::pluck('title')->toArray();
        $this->loadDefaultPlans();

        if (!$this->selectedPlanId && count($this->plans) > 0) {
            $this->selectedPlanId = $this->plans[0]['id'];
        }
    }

    protected function loadDefaultPlans()
    {
        $this->plans = Plan::orderBy('order_id')->get()->map(function ($plan) {
            return [
                'id' => $plan->id,
                'name' => $plan->name,
                'price' => (float) $plan->price,
                'day' => $plan->day,
                'credit_price'=>$plan->credit_price == 0 ? null : $plan->credit_price ,
                'discount' => 0,
                'discounted_price' => (float) $plan->price,
            ];
        })->toArray();

        $this->keepValidSelectedPlan();
    }

    protected function keepValidSelectedPlan()
    {
        if (count($this->plans) === 0) {
            $this->selectedPlanId = null;
            return;
        }

        $exists = collect($this->plans)->firstWhere('id', $this->selectedPlanId);

        if (!$exists) {
            $this->selectedPlanId = $this->plans[0]['id'];
        }
    }

    public function selectPlan($planId)
    {

        $plan = collect($this->plans)->firstWhere('id', (int) $planId);

        if (!$plan) {
            return;
        }

        $this->selectedPlanId = (int) $planId;
    }

    public function applyPromo()
    {
        $code = trim((string) $this->promocode);


        if ($code === '') {
            $this->promoStatus = 1;
            $this->promoMessage = 'Please enter promo code';
            $this->appliedCode = null;
            $this->loadDefaultPlans();
            return;
        }

        $base = Promocode::query()->where('code', $code);

        if (!(clone $base)->exists()) {
            $this->promoStatus = 3;
            $this->promoMessage = 'Promo code not found';
            $this->appliedCode = null;
            $this->loadDefaultPlans();
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
            $this->loadDefaultPlans();
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
            $this->loadDefaultPlans();
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
            $this->loadDefaultPlans();
            return;
        }

        $promo = (clone $base)
            ->with(['plans:id'])
            ->first(['id', 'code', 'discount']);

        if (!$promo) {
            $this->promoStatus = 3;
            $this->promoMessage = 'Promo code is invalid';
            $this->appliedCode = null;
            $this->loadDefaultPlans();
            return;
        }

        $discount = (float) ($promo->discount ?? 0);
        $promoPlanIds = $promo->plans->pluck('id')->toArray();

        $this->plans = Plan::orderBy('order_id')->get()->map(function ($plan) use ($promoPlanIds, $discount) {
            $hasDiscount = in_array($plan->id, $promoPlanIds);

            return [
                'id' => $plan->id,
                'name' => $plan->name,
                'price' => (float) $plan->price,
                'day' => $plan->day,
                'discount' => $hasDiscount ? $discount : 0,
                'credit_price'=>$plan->credit_price == 0 ? null : $plan->credit_price ,
                'discounted_price' => $hasDiscount
                    ? round(max(0, $plan->price * ((100 - $discount) / 100)), 2)
                    : (float) $plan->price,
            ];
        })->toArray();

        $this->promoStatus = 2;
        $this->promoMessage = 'Promo code applied successfully';
        $this->appliedCode = $promo->id;
        $this->appliedCodeName = $promo->code;
        $this->appliedCodeDiscount = $promo->discount;
        $this->promocode = '';

        $this->keepValidSelectedPlan();
    }

    public function purchase($paymentMethod)
    {

        $user = auth()->user();
        $plan = Plan::findOrFail($this->selectedPlanId);
        $price=$plan->price;
        $promoCodeId=$this->appliedCode;

        if ($promoCodeId){
            $promoCode=Promocode::with('plans')->find($promoCodeId);

            if ($promoCode->plans->contains('id', $plan->id)){
                $price=round($plan->price - ($plan->price*($promoCode->discount)/100),2);
            }else{
                $promoCodeId =null;
            }
        }

        $user->update([
            'tmp_payment_slug'=>$plan->id,
            'tmp_payment_price'=>$price,
            'tmp_payment_promocode'=>$promoCodeId ?? null,
            'tmp_payment_type'=>2,
        ]);

        $key = auth()->user()->id;
        $payment_url = "https://teacher-muzaffar.com";

        $url = "$payment_url/pay/$paymentMethod/$key/$price?redirect_url=".route('user.profile');

        $this->dispatch('open-payment-plan', url: $url);

    }

    public function freePlanActivate($planId)
    {
        $user = auth()->user();
        $plan = Plan::findOrFail($planId);
        $start_time = now()->format('Y-m-d H:i:s');
        $end_time = Carbon::parse($start_time)->addDays($plan->day);
        $promoCodeId=$this->appliedCode;
        $today = now();

        if ($promoCodeId){
            $promoCode=Promocode::find($promoCodeId);
            $price=round($plan->price - ($plan->price*($promoCode->discount)/100),2);
        }

        $promoCode = PromoCode::find($promoCodeId);


        $existingPlan = PremiumToUsers::where('user_id', $user->id)
            ->where(function ($query) use ($today) {
                $query->where('start_time', '<=', $today)
                    ->where('end_time', '>=', $today);
            })
            ->latest()
            ->first();

        if ($existingPlan) {
            $start_time = $existingPlan->end_time;
            $end_time = Carbon::parse($start_time)->addDays($plan->day);

        }

        if ($plan->credit_price>0){
            $this->createBalanceEditByPlan($user,$plan,$plan->credit_price);
        }

        $existingPlan = PremiumToUsers::create([
            'user_id' => $user->id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'type' => '2',
            'price' => $plan->price,
            'admin_id' => null,
            'plan_id' => $plan->id,
            'discount' => 100,
            'promocode_id' => $promoCodeId,
        ]);


        $this->reset('promocode','appliedCodeDiscount','promoMessage','appliedCode','appliedCodeName','selectedPlanId','promoStatus');
        $this->loadDefaultPlans();

        $this->dispatch('balance-updated');
        $this->dispatch('global_alert', 1, "Free premium plan activated successfully");

    }
    public function render()
    {
        return view('livewire.user.premium.subscription-livewire');
    }
}
