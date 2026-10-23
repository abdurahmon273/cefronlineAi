<?php

namespace App\Livewire\Admin\Package;

use App\Models\BalanceEdit;
use App\Models\GlobalSetting;
use App\Models\Package;
use App\Models\Promocode;
use App\Models\User;
use App\Traits\BillingTrait;
use Livewire\Component;

class PackageUsersEditLivewire extends Component
{
    use BillingTrait;

    public $packages=[],$users=[];
    public $user_id,$promoCode,$package_id,$start_date;

    public $search,$founded_users=[],$chosen_user,$packageToUsers;

    public $isSaving = false;


    protected $listeners=[
        'choosen_user'
    ];

    protected $messages = [
        'package_id.required' => 'Plan tanlanishi shart',
        'user_id.required' => 'Foydalanuvchi tanlanishi shart',
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

            $user = User::find($this->user_id);
            $package = Package::find($this->package_id);
            $promoCodeId=null;
            $discount=0;

            if ($user && $package) {
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
                        if (!in_array($package->id,$promoCode->packages->pluck('id')->toArray())) {
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


                $globalSetting = GlobalSetting::orderByDesc('effective_at')->select('chat_gpt_tax','profit_tax','usd_current_currency')->firstOrFail();

                $this->changeUserBalanceByAdmin($this->user_id,(-$this->packageToUsers->price));

                $this->packageToUsers->update([
                    'price' => $package->price,
                    'value' =>$package->credit_price,
                    'current_currency'=>$globalSetting->usd_current_currency,
                    'current_tax'=>$globalSetting->chat_gpt_tax+$globalSetting->profit_tax,
                    'current_balance'=>$user->balance,
                    'admin_id' => auth()->user()->id,
                    'package_id'=>$package->id,
                    'promocode_id'=>$promoCodeId,
                    'discount'=>$discount,
                ]);

                $this->changeUserBalanceByAdmin($this->user_id,$package->price);

                return redirect(route('admin.package_users.index'));
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->isSaving = false;
            throw $e;
        } catch (\Exception $e) {
            $this->isSaving = false;
        }
    }

    public function mount($packageToUsers)
    {
        $this->packages = Package::get()
            ->mapWithKeys(function ($package) {
                return [
                    $package->id => [
                        'id' => $package->id,
                        'price' => $package->price,
                        'writing_count' => $package->writing_count,
                        'speaking_count' => $package->speaking_count,
                    ]
                ];
            })
            ->toArray();
        $this->users = User::pluck('name', 'id')->toArray();

        $this->chosen_user = [
            'id' => $packageToUsers->user->id,
            'name' => $packageToUsers->user->name . ' ' . $packageToUsers->user->surname,
            'phone_number' => $packageToUsers->user->mobil_number,
        ];
        $this->package_id = $packageToUsers->package_id;
        $this->promoCode=$packageToUsers->promoCode? $packageToUsers->promoCode->code :'';


        $this->packageToUsers = $packageToUsers;
        $this->user_id = $packageToUsers->user_id;
    }


    public function render()
    {
        return view('livewire.admin.package.package-users-edit-livewire');
    }
}
