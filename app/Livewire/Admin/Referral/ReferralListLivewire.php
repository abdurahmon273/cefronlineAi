<?php

namespace App\Livewire\Admin\Referral;

use App\Exports\ReferralUsersExport;
use App\Models\GlobalSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Date;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class ReferralListLivewire extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $start_date, $end_date;
    public $search = '';
    public $type;
    public $paginate = 50;
    public $showFilters = false;
    public $availableReferralMonths = [];
    public $selectedReferralMonth;

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }
    protected $listeners=[
        'updated_start_date',
        'updated_end_date',
    ];

    public function updated_start_date($date)
    {
        $this->start_date = $date;
    }
    public function updated_end_date($date){
        $this->end_date = $date;
    }

    public function export()
    {
        return Excel::download(
            new ReferralUsersExport($this->search, $this->start_date, $this->end_date),
            'referral_users.xlsx'
        );
    }

    public function updatedSelectedReferralMonth($value)
    {
        if (!$value) {
            return;
        }

        $selectedMonth = Carbon::createFromFormat('Y-m', $value)->startOfMonth();

        $this->start_date = $selectedMonth->toDateString();
        $this->end_date = $selectedMonth->copy()->endOfMonth()->toDateString();
        $this->resetPage();
    }

    public function mount()
    {
        $globalSetting = GlobalSetting::orderByDesc('effective_at')
            ->select('start_referral_date')
            ->firstOrFail();

        $this->availableReferralMonths = $this->buildReferralMonths($globalSetting->start_referral_date);
        $this->selectedReferralMonth = now()->format('Y-m');

        $date = now()->startOfMonth();
        $this->start_date = $date->toDateString();
        $this->end_date = $date->copy()->endOfMonth()->toDateString();
    }

    private function buildReferralMonths($startReferralDate): array
    {
        $startMonth = Carbon::parse($startReferralDate)->startOfMonth();
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
            ];

            $startMonth->addMonth();
        }

        return $months;
    }
    public function render()
    {
        $profitPercentage = GlobalSetting::orderByDesc('effective_at')->select('referral_profit_percentage')->firstOrFail()->referral_profit_percentage;


        $users = User::where('id', '!=', 1)
            ->whereHas('referral_users', function($q){
                $q->when($this->start_date, function($subQ){
                  return $subQ->where('created_at', '>=', Date::parse($this->start_date)->startOfDay());
                })->when($this->end_date, function($subQ){
                    return $subQ->where('created_at', '<=', Date::parse($this->end_date)->endOfDay());
                });
            })
            ->whereHas('referral_users.firstPremium')
            ->with(['referral_users'=>function($referral_users){
                return $referral_users->with('firstPremium')
                    ->where('created_at', '>=', Date::parse($this->start_date)->startOfDay())
                    ->where('created_at', '<=', Date::parse($this->end_date)->endOfDay());
            }])
            ->when($this->search, function ($users) {
                $search = strtolower($this->search); // Katta-kichik harfni farqlamaslik uchun
                $users->where(function ($subQuery) use ($search) {
                    $subQuery->orWhereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(surname) LIKE ?', ["%{$search}%"])
                        ->orWhere('mobil_number', 'like', "%{$search}%"); // Telefon raqami bo‘yicha qidirish
                });
            })
            ->orderByDesc('id')
            ->paginate($this->paginate)
            ->through(function ($user) use ($profitPercentage) {
                $percentageSum=$profitPercentage /100;
                $activePrice = $user->referral_users
//                    ->where('created_at', '>=', $this->start_date)
//                    ->where('created_at', '<=', $this->end_date)
                    ->whereNull('referral_expired_at')
                    ->sum(function ($refUser) {
                        return $refUser->firstPremium
                            ? $refUser->firstPremium->price / 100 * (100 - $refUser->firstPremium->discount)
                            : 0;
                    });

                $paidPrice = $user->referral_users
//                    ->where('created_at', '>=', $this->start_date)
//                    ->where('created_at', '<=', $this->end_date)
                    ->whereNotNull('referral_expired_at')
                    ->sum(function ($refUser) {
                        return $refUser->firstPremium
                            ? $refUser->firstPremium->price / 100 * (100 - $refUser->firstPremium->discount)
                            : 0;
                    });

                $user->active_price = $activePrice * $percentageSum;
                $user->paid_price = $paidPrice * $percentageSum;

                $user->totalPrice = $user->active_price + $user->paid_price;

                return $user;
            });

        $filteredActiveSum = $users->getCollection()->sum('active_price');
        $filteredPaidSum = $users->getCollection()->sum('paid_price');

        return view('livewire.admin.referral.referral-list-livewire',compact('users','filteredActiveSum','filteredPaidSum'));
    }
}
