<?php

namespace App\Livewire\User\Setting;

use App\Models\BalanceEdit;
use App\Models\GlobalSetting;
use App\Models\PremiumToUsers;
use App\Models\User;
use App\Traits\BillingTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class ProfileSettingLivewire extends Component
{
    use BillingTrait;

    public $toggleB=true;
    public $user;
    public $name;
    public $surname;
    public $mobil_number,$mobil_number_or_email;
    public $globalSetting;

    public $premiumToUser,$hasPremium,$lastBalanceUpdate;

    public $password,$new_password,$confirm_password;

    public $persentageData=['success'=>false,'exists'=>0,'used'=>0];

    public $referralCount = 0;
    public $activeReferralCount = 0,$referralLink,$adminContactLink,$startReferralDate,$currentReferralDate;
    public $totalReferralBalance = 0,$paidReferralBalance=0,$referralBalance=0;
    public $availableReferralMonths = [];
    public $selectedReferralMonth;
    public $recentReferrals = [];
    public $recentReferralLimit = 3;

    public $tab = 'personal';

    public function setTab($tab)
    {
        if ($this->tab === $tab) {
            return;
        }

        if ($tab=='personal' || $tab=='security'){

          $this->mountPersonal();
        }elseif ($tab=='premium'){

            $this->mountPremium();
        }elseif ($tab=='referral'){

            $this->mountReferral();
        }

        $this->tab = $tab;
    }

    public function mountPersonal()
    {
        $user = auth()->user();
        $this->name=$user->name;
        $this->surname=$user->surname;

        if ($user->mobil_number){
            $this->mobil_number_or_email = $this->formatUzPhone($user->mobil_number);

        }elseif ($user->email){
            $this->mobil_number_or_email = $user->email;

        }
    }
    public function mountPremium()
    {
        $today = now();

        $this->premiumToUser=PremiumToUsers::where('user_id', $this->user->id)
            ->where(function ($query) use ($today) {
                $query->where('start_time', '<=', $today)
                    ->where('end_time', '>=', $today);
            })
            ->orderByDesc('id')
            ->first();

        $balanceEdit = BalanceEdit::where('user_id', $this->user->id)
            ->select('id', 'price')
            ->latest('id')
            ->get();

        $allBalance = $balanceEdit->sum('price');

        $this->persentageData = $this->getBalancePersentageData($allBalance, $this->user);

        $this->lastBalanceUpdate = $balanceEdit->first()?->price;

        if($this->premiumToUser){
            $this->hasPremium=true;
        }
        else{
            $this->hasPremium=false;
            $this->premiumToUser=PremiumToUsers::where('user_id', $this->user->id)
                ->orderByDesc('id')
                ->first();
        }
    }
    public function mountReferral()
    {
        $this->referralLink = route('register', ['id'=>$this->user->id]);
        $this->globalSetting = GlobalSetting::orderByDesc('effective_at')->select('id', 'referral_profit_percentage', 'admin_contact', 'start_referral_date')->firstOrFail();
        $this->startReferralDate=$this->globalSetting->start_referral_date;
        $this->adminContactLink=$this->globalSetting->admin_contact;
        $this->availableReferralMonths = $this->buildReferralMonths();
        $this->selectedReferralMonth = now()->format('Y-m');
        $this->currentReferralDate = now()->startOfMonth()->toDateString();

        $this->generateReferalData($this->currentReferralDate);

    }

    public function updatedSelectedReferralMonth($value)
    {
        if (!$value) {
            return;
        }

        $selectedDate = Carbon::createFromFormat('Y-m', $value)->startOfMonth();

        $this->currentReferralDate = $selectedDate->toDateString();
        $this->generateReferalData($this->currentReferralDate);
    }

    private function buildReferralMonths(): array
    {
        $startMonth = Carbon::parse($this->startReferralDate)->startOfMonth();
        $currentMonth = now()->startOfMonth();
        $months = [];
        $monthNames = [
            1 => 'yanvar',
            2 => 'fevral',
            3 => 'mart',
            4 => 'aprel',
            5 => 'may',
            6 => 'iyun',
            7 => 'iyul',
            8 => 'avgust',
            9 => 'sentabr',
            10 => 'oktabr',
            11 => 'noyabr',
            12 => 'dekabr',
        ];

        while ($startMonth->lte($currentMonth)) {
            $months[] = [
                'value' => $startMonth->format('Y-m'),
                'label' => $startMonth->year . ',' . $monthNames[(int) $startMonth->month],
                'selected' => now()->startOfMonth() ==$startMonth ? true : false,
            ];

            $startMonth->addMonth();
        }

        return $months;
    }

    public function generateReferalData($date)
    {

        $startDate=Date::parse($date)->startOfMonth();
        $endDate=Date::parse($date)->endOfMonth();

        $percentage = $this->globalSetting->referral_profit_percentage;
        $referralBaseQuery = User::query()->where('created_by_id', $this->user->id);

        $this->referralCount = (clone $referralBaseQuery)->count();

        $referralIds = (clone $referralBaseQuery)
            ->whereNull('referral_expired_at')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->pluck('id')
            ->toArray();

        $paidReferralIds = (clone $referralBaseQuery)
            ->whereNotNull('referral_expired_at')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->pluck('id')
            ->toArray();

        $this->activeReferralCount = count($referralIds);

        $price = PremiumToUsers::whereIn('user_id', $referralIds)
            ->orderBy('start_time', 'asc')
            ->select('id', 'price','start_time','discount','user_id')
            ->get()
            ->groupBy('user_id')
            ->map(function ($items) {
                return $items->first();
            })
            ->sum(function ($item) {
                return $item->price / 100 * ( 100 - $item->discount );
            });

        $paidPrice = PremiumToUsers::whereIn('user_id', $paidReferralIds)
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

                return ($item->price)
                    * ((100 - $item->discount) / 100)
                    * ($referralPercentage / 100);
            });

        $this->referralBalance = $price * (100 - $percentage) / 100;
        $this->paidReferralBalance = $paidPrice ;
        $this->totalReferralBalance=$this->referralBalance + $this->paidReferralBalance ;
        $this->loadRecentReferrals();
    }

    public function loadRecentReferrals()
    {
        $date=$this->currentReferralDate ?? now();
        $startDate=Date::parse($date)->startOfMonth();
        $endDate=Date::parse($date)->endOfMonth();

        $users = User::query()
            ->where('created_by_id', $this->user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('id', 'name', 'surname', 'referral_expired_at')
            ->latest('id')
            ->limit($this->recentReferralLimit)
            ->get();

        $premiumRows = PremiumToUsers::query()
            ->whereIn('user_id', $users->pluck('id'))
            ->orderBy('start_time', 'asc')
            ->select('user_id', 'price', 'discount')
            ->get()
            ->groupBy('user_id')
            ->map(function ($items) {
                $item = $items->first();

                return $item ? ($item->price / 100 * (100 - $item->discount)) : 0;
            });

        $this->recentReferrals = $users
            ->mapWithKeys(function ($user) use ($premiumRows) {
                $fullName = trim(($user->name ?? '') . ' ' . ($user->surname ?? '')) ?: 'User #' . $user->id;

                return [
                    $user->id => [
                        'fullname' => $fullName,
                        'price' => (float) ($premiumRows[$user->id] ?? 0),
                        'status' => $user->referral_expired_at ? 'paid' : 'active',
                    ],
                ];
            })
            ->toArray();
    }

    public function showMoreReferrals()
    {
        if (count($this->recentReferrals) >= $this->referralCount) {
            return;
        }

        $this->recentReferralLimit += 3;
        $this->loadRecentReferrals();
    }

    public function mount()
    {
        $this->user=auth()->user();

        $this->mountPersonal();


    }

    private function formatUzPhone($phone)
    {
        $phone = preg_replace('/^\+998/', '', $phone);

        return preg_replace(
            '/(\d{2})(\d{3})(\d{2})(\d{2})/',
            '($1) $2-$3-$4',
            $phone
        );
    }

    public function maskPhone($phone)
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if (strlen($digits) < 4) {
            return $phone;
        }

        return '+' . substr($digits, 0, 3) . ' ' . substr($digits, 3, 2) . ' *** ** ' . substr($digits, -2);
    }

    public function saveFullName()
    {
        $this->resetErrorBag();

        $validated = $this->validateFullName();

        if (!$validated) {
            return;
        }

        $user = auth()->user();
        $user->name = $this->name;
        $user->surname = $this->surname;
        $user->save();

        session()->flash('success_fullname', 'Full name updated successfully.');
    }


    public function saveNewPassword()
    {
        $this->resetErrorBag();

        $validated = $this->validateChangePassword();
        if (!$validated) return;

        $user = auth()->user();
        $user->password = Hash::make($this->new_password);
        $user->save();

        // inputlarni tozalash
        $this->password = null;
        $this->new_password = null;
        $this->confirm_password = null;

        session()->flash('success_password', 'Password updated successfully.');
    }

    public function validateChangePassword()
    {
        $user = auth()->user();

        // old password
        if (!$this->password || mb_strlen($this->password) < 6) {
            $this->addError('password', 'Previous password is required.');
            return false;
        }

        if (!Hash::check($this->password, $user->password)) {
            $this->addError('password', 'Previous password is incorrect.');
            return false;
        }

        // new password
        if (!$this->new_password || mb_strlen($this->new_password) < 6) {
            $this->addError('new_password', 'New password must be at least 6 characters.');
            return false;
        }

        if ($this->new_password === $this->password) {
            $this->addError('new_password', 'New password must be different from previous password.');
            return false;
        }

        // confirm
        if ($this->confirm_password !== $this->new_password) {
            $this->addError('confirm_password', 'Passwords do not match.');
            return false;
        }

        return true;
    }

    public function validateFullName()
    {
        if (strlen($this->name)<3){
            $this->addError('name','name ');
            return false;
        }

        if (strlen($this->surname)<3){
            $this->addError('surname','surname ');
            return false;
        }


        return true;
    }

    public function render()
    {
        return view('livewire.user.setting.profile-setting-livewire');
    }
}
