<?php

namespace App\Livewire\Premium\PremiumUsers;

use App\Models\Plan;
use App\Models\PremiumToUsers;
use App\Models\Promocode;
use App\Models\User;
use App\Traits\BillingTrait;
use Carbon\Carbon;
use Livewire\Component;

class PremiumUsersCreateLivewire extends Component
{
    use BillingTrait;

    public $plans=[];
    public $user_id,$promoCode,$plan_id,$start_date;

    public $search,$founded_users=[],$chosen_user;

    public $isSaving = false;

    protected $listeners=[
        'choosen_user'
    ];

    protected $messages = [
        'plan_id.required' => 'Plan tanlanishi shart',
        'user_id.required' => 'Foydalanuvchi tanlanishi shart',
        'start_date.required' => 'Sana kiritilishi shart',
        'start_date.after_or_equal' => 'Sana bugungi kundan kichik bo\'lmasligi kerak',
    ];

    public function choosen_user($id)
    {
        $this->user_id=$id;
        $this->skipRender();
    }

    public function saqlash()
    {
        $this->isSaving = true;

        try {
            $validatedData = $this->validate([
                'plan_id' => 'required|exists:plans,id',
                'user_id' => 'required|exists:users,id',
                'start_date' => ['required', 'date', 'after_or_equal:today'],
                'promoCode'=> 'nullable|exists:promocodes,code',
            ]);

            $user = User::find($this->user_id);
            $plan = Plan::find($this->plan_id);
            $startDate = Carbon::parse($this->start_date);  // Convert to Carbon instance
            $dayCount = (int) $plan->day;  // Convert plan->day to integer
            $endDate = (clone $startDate)->addDays($dayCount);  // Clone before adding days
            $promoCodeId=null;
            $discount=0;



            $today = now();
            $existingPlan = PremiumToUsers::where('user_id', $user->id)
                ->where('deleted_at', null)
                ->where(function ($query) use ($today,$startDate) {
                    $query->where('start_time', '<=', $startDate)
                        ->where('end_time', '>=', $startDate);
                })
                ->latest()
                ->first();

            if ($existingPlan) {
                $startDate = $existingPlan->end_time;
                $endDate = Carbon::parse($startDate)->addDays($plan->day);


            }


            if ($user && $plan) {

                if ($this->promoCode){
                    $promoCode = Promocode::with(['plans'])
                        ->where('code', $this->promoCode)
                        ->where('start_date', '<',now())
                        ->where('end_date', '>',now())
                        ->when(true, function ($q) {
                            $q->where(function ($sub) {
                                $sub->where(function ($inner) {
                                    $inner->where('type', 0)
                                        ->whereDoesntHave('premium_to_users');
                                })
                                    ->orWhere('type', '!=', 0);
                            });
                        })
                        ->first();


                    if ($promoCode){
                        if (!in_array($plan->id,$promoCode->plans->pluck('id')->toArray())) {
                            $this->addError('promoCode', 'This promo code not avalible for this plan.');
                            $this->isSaving = false;
                            return;
                        }
                        $promoCodeId=$promoCode->id;
                        $discount=$promoCode->discount;
                    }else{
                        $this->addError('promoCode', 'Promo Code not found.(already used or date expired )');
                        $this->isSaving = false;
                        return;
                    }
                }

                if ($plan->credit_price>0){
                    $this->createBalanceEditByPlan($user,$plan,$plan->credit_price);
                }

                $premium_user=PremiumToUsers::create([
                    'user_id' => $this->user_id,
                    'start_time' => $startDate,
                    'end_time' => $endDate,
                    'type' => '1',
                    'price' => $plan->price,
                    'admin_id' => auth()->user()->id,
                    'plan_id' => $plan->id,
                    'promocode_id' => $promoCodeId,
                    'discount' => $discount,
                ]);

                return redirect(route('admin.premium_users.index'));
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->isSaving = false; // Validatsiya xatoligi bo'lganda ham loading holatini saqlaymiz
            throw $e; // Xatolikni qayta tashlash orqali view'da ko'rsatamiz
        } catch (\Exception $e) {
            $this->isSaving = false;
        }
    }

    public function mount()
    {
        $this->start_date = Carbon::now()->format('Y-m-d\TH:i'); // Initializes with current date and time
        $this->plans = Plan::pluck('name', 'id')->toArray();
    }

    public function render()
    {
        return view('livewire.premium.premium-users.premium-users-create-livewire');
    }
}
