<?php

namespace App\Livewire\Admin\Referral;

use App\Models\GlobalSetting;
use App\Models\PremiumToUsers;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class ReferralIndexLivewire extends Component
{
    use WithPagination;

    public $selectedUserId = '';
    public $selectedUserLabel = '';
    public $search = '';
    public $dropdownOpen = false;
    public $referralLink = '';
    public $price = null,$plan;
    public $percentage = 0;
    public $hasPremiumPrice = false;
    public $activeReferralCount = 0;
    public $referralCount = 0;
    public $referralBalance = 0;
    public $paidReferralBalance = 0;
    public $totalReferralBalance = 0;
    public $loadError = null;
    public $start_date, $end_date;
    public $recentReferrals = [];
    public $recentReferralLimit = 3;

    protected $paginationTheme = 'bootstrap';

    public function mount($id=null,$startDate=null,$endDate=null)
    {
        $this->percentage = (float) (GlobalSetting::query()
            ->orderByDesc('effective_at')
            ->value('referral_profit_percentage') ?? 0);
        $this->start_date = $startDate;
        $this->end_date = $endDate;

        if ($id){
            $this->selectUser($id);
        }
    }
    protected $listeners=[
        'updated_start_date',
        'updated_end_date',
    ];

    public function updated_start_date($date)
    {
        $this->start_date = $date;
        if ($this->selectedUserId){
            $this->recentReferralLimit = 3;
            $this->loadReferralData($this->selectedUserId);
        }
    }
    public function updated_end_date($date){
        $this->end_date = $date;
        if ($this->selectedUserId){
            $this->recentReferralLimit = 3;
            $this->loadReferralData($this->selectedUserId);
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
        $this->dropdownOpen = true;
    }

    public function openDropdown()
    {
        $this->dropdownOpen = true;
    }

    public function selectUser($userId)
    {
        $user = User::query()
            ->select('id', 'name', 'surname', 'mobil_number')
            ->find($userId);

        if (!$user) {
            return;
        }

        $this->selectedUserId = (string) $user->id;
        $this->selectedUserLabel = trim(($user->name ?? '') . ' ' . ($user->surname ?? '')) ?: 'User #' . $user->id;

        if ($user->mobil_number) {
            $this->selectedUserLabel .= ' - ' . $user->mobil_number;
        }

        $this->search = $this->selectedUserLabel;
        $this->dropdownOpen = false;
        $this->recentReferralLimit = 3;
        $this->loadReferralData($userId);

    }

    public function updatedSelectedUserId($value)
    {
        $this->loadReferralData($value);
    }

    protected function resetReferralData()
    {
        $this->price = null;
        $this->hasPremiumPrice = false;
        $this->activeReferralCount = 0;
        $this->referralCount = 0;
        $this->referralBalance = 0;
        $this->paidReferralBalance = 0;
        $this->totalReferralBalance = 0;
        $this->referralLink = '';
        $this->loadError = null;
        $this->recentReferrals = [];
    }

    protected function loadReferralData($value)
    {
        $this->resetReferralData();

        if (!$value) {
            return;
        }

//        try {\
        $today=now();
            $user = User::query()
                ->select('id')
                ->findOrFail($value);

            $this->referralLink = route('register', $user->id);

            $globalSetting = GlobalSetting::query()
                ->orderByDesc('effective_at')
                ->select('referral_profit_percentage')
                ->firstOrFail();

            $this->percentage = (float) $globalSetting->referral_profit_percentage;

            $referredUsers = User::query()
                ->when($this->start_date , function ($query) {
                    $query->where('created_at', '>=', $this->start_date);
                })
                ->when($this->end_date , function ($query) {
                    $query->where('created_at', '<=', $this->end_date);
                })
                ->where('created_by_id', $user->id)
                ->select('id', 'name', 'surname', 'created_at', 'referral_expired_at')
                ->addSelect('mobil_number')
                ->latest('id')
                ->get();

            $referralIds = $referredUsers
                ->whereNull('referral_expired_at')
                ->pluck('id')
                ->values()
                ->all();

            $paidReferralIds = $referredUsers
                ->whereNotNull('referral_expired_at')
                ->pluck('id')
                ->values()
                ->all();

            $this->activeReferralCount = count($referralIds);
            $this->referralCount = $referredUsers->count();

            $premium = PremiumToUsers::query()
                ->with(['plan'])
                ->where('user_id', $user->id)
                ->where('start_time', '<',$today)
                ->where('end_time', '>',$today)
                ->first();

            if ($premium) {
                $this->hasPremiumPrice = true;
                $this->price = ($premium->price / 100) * (100 - $premium->discount);
                $this->plan = $premium->plan->name;

            }

            $price = PremiumToUsers::query()
                ->whereIn('user_id', $referralIds)
                ->orderBy('start_time', 'asc')
                ->select('id', 'price', 'start_time', 'discount', 'user_id')
                ->get()
                ->groupBy('user_id')
                ->map(function ($items) {
                    return $items->first();
                })
                ->sum(function ($item) {
                    return $item->price / 100 * (100 - $item->discount);
                });

        $paidPrice = PremiumToUsers::query()
            ->whereIn('user_id', $paidReferralIds)
            ->with('user:id,current_referral_percentage')
            ->orderBy('start_time', 'asc')
            ->select('id', 'price', 'start_time', 'discount', 'user_id')
            ->get()
            ->groupBy('user_id')
            ->map(function ($items) {
                return $items->first();
            })
            ->sum(function ($item) {
                $referralPercentage = $item->user->current_referral_percentage ?? 50;

                return ($item->price / 100)
                    * ((100 - $item->discount) / 100)
                    * ($referralPercentage / 100);
            });

        $this->referralBalance = $price * ($this->percentage) / 100;
        $this->paidReferralBalance = $paidPrice;
            $this->totalReferralBalance = $this->referralBalance + $this->paidReferralBalance;

            $firstPremiums = PremiumToUsers::query()
                ->with(['plan:id,name', 'promoCode:id,code,discount'])
                ->whereIn('user_id', $referredUsers->pluck('id'))
                ->orderBy('start_time', 'asc')
                ->select('id', 'user_id', 'plan_id', 'promocode_id', 'price', 'discount', 'start_time')
                ->get()
                ->groupBy('user_id')
                ->map(function ($items) {
                    return $items->first();
                });

            $this->recentReferrals = $referredUsers
                ->take($this->recentReferralLimit)
                ->map(function ($referralUser) use ($firstPremiums) {
                    $firstPremium = $firstPremiums->get($referralUser->id);
                    $fullName = trim(($referralUser->name ?? '') . ' ' . ($referralUser->surname ?? '')) ?: 'User #' . $referralUser->id;
                    $grossPrice = (float) ($firstPremium->price ?? 0);
                    $discount = (float) ($firstPremium->discount ?? 0);
                    $netPrice = $firstPremium ? ($grossPrice / 100) * (100 - $discount) : 0;

                    return [
                        'id' => $referralUser->id,
                        'fullname' => $fullName,
                        'phone' => $referralUser->mobil_number,
                        'plan' => $firstPremium?->plan?->name,
                        'purchase_date' => $firstPremium?->start_time,
                        'promo_code' => $firstPremium?->promoCode?->code,
                        'discount' => $discount,
                        'original_price' => $grossPrice,
                        'net_price' => $netPrice,
                        'referral_profit' => $netPrice * ($this->percentage) / 100,
                        'status' => $referralUser->referral_expired_at ? 'paid' : 'active',
                    ];
                })
                ->values()
                ->toArray();
//        } catch (\Throwable $e) {
//            report($e);
//            $this->loadError = 'Referral data could not be loaded.';
//        }
    }

    public function showMoreReferrals()
    {
        if (count($this->recentReferrals) >= $this->referralCount || !$this->selectedUserId) {
            return;
        }

        $this->recentReferralLimit += 3;
        $this->loadReferralData($this->selectedUserId);
    }

    public function payReferral()
    {
        if (!$this->selectedUserId || $this->referralBalance <= 0){
            return;
        }

        try {
            $userId = $this->selectedUserId;
            $activeReferralIds = User::where('created_by_id', $userId)
                ->when($this->start_date , function ($query) {
                    $query->where('created_at', '>=', $this->start_date);
                })
                ->when($this->end_date , function ($query) {
                    $query->where('created_at', '<=', $this->end_date);
                })
                ->whereNull('referral_expired_at')
                ->pluck('id');

            $eligibleUserIds = PremiumToUsers::query()
                ->whereIn('user_id', $activeReferralIds)
                ->orderBy('start_time', 'asc')
                ->select('user_id', 'price', 'discount')
                ->get()
                ->groupBy('user_id')
                ->map(function ($items) {
                    $item = $items->first();

                    return $item ? ($item->price / 100 * (100 - $item->discount)) : 0;
                })
                ->filter(function ($price) {
                    return $price > 0;
                })
                ->keys();

            $users= User::whereIn('id', $eligibleUserIds)->get();
            $today=now();

            foreach ($users as $user) {
                $user->referral_expired_at = $today;
                $user->current_referral_percentage = $this->percentage;
                $user->save();
            }

            $this->loadReferralData($userId);
        } catch (\Throwable $e) {
            report($e);
            $this->loadError = 'Payment could not be completed.';
        }
    }

    public function render()
    {
        $users = User::query()
            ->select('id', 'name', 'surname', 'mobil_number')
            ->when($this->search, function ($query) {
                $term = trim($this->search);
                $query->where(function ($subQuery) use ($term) {
                    $subQuery->where('name', 'like', '%' . $term . '%')
                        ->orWhere('surname', 'like', '%' . $term . '%')
                        ->orWhereRaw("CONCAT(COALESCE(name, ''), ' ', COALESCE(surname, '')) like ?", ['%' . $term . '%'])
                        ->orWhere('mobil_number', 'like', '%' . preg_replace('/\s+/', '', $term) . '%');
                });
            })
            ->orderByDesc('id')
            ->paginate(20);

        return view('livewire.admin.referral.referral-index-livewire', [
            'users' => $users,
        ]);
    }
}
