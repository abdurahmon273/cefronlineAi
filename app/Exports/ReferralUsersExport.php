<?php

namespace App\Exports;

use App\Models\GlobalSetting;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Date;
use Maatwebsite\Excel\Concerns\FromView;

class ReferralUsersExport implements FromView
{
    protected $search;
    protected $start_date;
    protected $end_date;

    public function __construct($search, $start_date = null, $end_date = null)
    {
        $this->search = $search;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    public function view(): View
    {
        $profitPercentage = GlobalSetting::orderByDesc('effective_at')
            ->select('referral_profit_percentage')
            ->firstOrFail()
            ->referral_profit_percentage;

        $percentageSum = $profitPercentage / 100;

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
            ->get()
            ->map(function ($user) use ($percentageSum) {
                $activePrice = $user->referral_users
                    ->whereNull('referral_expired_at')
                    ->sum(function ($refUser) {
                        return $refUser->firstPremium
                            ? $refUser->firstPremium->price / 100 * (100 - $refUser->firstPremium->discount)
                            : 0;
                    });

                $paidPrice = $user->referral_users
                    ->whereNotNull('referral_expired_at')
                    ->sum(function ($refUser) {
                        return $refUser->firstPremium
                            ? $refUser->firstPremium->price / 100 * (100 - $refUser->firstPremium->discount)
                            : 0;
                    });

                $user->active_price = (int) round($activePrice * $percentageSum);
                $user->paid_price = (int) round($paidPrice * $percentageSum);
                $user->total_price = $user->active_price + $user->paid_price;

                return $user;
            });

        return view('exports.referral_users', compact('users'));
    }
}
