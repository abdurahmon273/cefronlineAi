<?php

namespace App\Livewire\Premium\PremiumUsers;

use App\Models\Plan;
use App\Models\PremiumToUsers;
use App\Models\Promocode;
use App\Models\User;
use App\Traits\BillingTrait;
use Carbon\Carbon;
use Livewire\Component;

class PremiumUsersEditLivewire extends Component
{
    use BillingTrait;

    public $plans=[];
    public $user_id,$promoCode,$plan_id,$start_date,$premium_to_user;

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
        $this->user_id = $user[0]['id'];
    }

    public function updatedSearch($search)
    {
        $this->founded_users = User::where(function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('surname', 'like', '%' . $this->search . '%')
                ->orWhere('mobil_number', 'like', '%' . $this->search . '%');
        })
            ->take(20) // Limits the query to 20 results
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name . ' ' . $user->surname,
                    'phone_number' => $user->mobil_number,
                ];
            });
    }


    public function saqlash()
    {
        $this->validate([
            'plan_id' => 'required|exists:plans,id',
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
        ]);

        $user = User::find($this->user_id);
        $plan = Plan::find($this->plan_id);
        $startDate = Carbon::parse($this->start_date);  // Convert to Carbon instance
        $endDate = $startDate->addDays( (int) $plan->day)->format('Y-m-d\TH:i');
        $promoCodeId=null;
        $discount=0;

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


            if ($plan->credit_price>0 && (! $this->premium_to_user->paln_id == $plan->id)){
                $this->createBalanceEditByPlan($user,$plan,$plan->credit_price);
            }

                $this->premium_to_user->update([
                    'user_id'=>$this->user_id,
                    'start_time'=>$this->start_date,
                    'end_time'=>$endDate,
                    'price'=>$plan->price,
                    'admin_id'=>auth()->user()->id,
                    'plan_id'=>$plan->id,
                    'promocode_id'=>$promoCodeId,
                    'discount'=>$discount,
                ]);

            return redirect(route('admin.premium_users.index'));

        }else {

        }
    }

    public function mount($premium_to_user)
    {
        $this->start_date = $premium_to_user->start_time ; // Initializes with current date and time
        $this->plans = Plan::pluck('name', 'id')->toArray();
        $this->premium_to_user=$premium_to_user;
        $this->plan_id=$premium_to_user->plan_id;
        $this->user_id=$premium_to_user->user_id;
        $this->promoCode=$premium_to_user->promoCode? $premium_to_user->promoCode->code :'';
        $this->chosen_user = [
            'id' => $premium_to_user->user->id,
            'name' => $premium_to_user->user->name . ' ' . $premium_to_user->user->surname,
            'phone_number' => $premium_to_user->user->mobil_number,
        ];
    }


    public function render()
    {
        return view('livewire.premium.premium-users.premium-users-edit-livewire');
    }
}
