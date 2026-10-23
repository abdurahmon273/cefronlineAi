<?php

namespace App\Livewire\Admin\Dashboard;

use App\Models\BalanceEdit;
use App\Models\GlobalSetting;
use App\Models\PremiumToUsers;
use App\Models\TotalVideoView;
use App\Models\User;
use App\Models\Video;
use App\Models\VideoWached;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DashboardIndexLivewire extends Component
{
    public $UserCount,$OneMonthUserCount,$OneYearUserCount;

    public $FilterMonth,$FilterYear;

    public $mostViewedVideo,$mostViewedSave;

    public $allPremiumSum,$allCreditSum,$allPaidReferralSum,$allReferralSum;

    public function mount()
    {
        $this->FilterMonth=Carbon::now()->month;
        $this->FilterYear=Carbon::now()->year;

        $this->Generate_Video_List();

        $this->generate_user_details();

        $this->Generate_Charts();

    }

    public function updatedFilterYear($value)
    {
        $this->generate_user_details();

        $this->Generate_Charts();

        $this->Generate_Video_List();

    }
    public function updatedFilterMonth($value)
    {
        $this->generate_user_details();

        $this->Generate_Charts();

        $this->Generate_Video_List();

    }


    public function Generate_Charts()
    {

        $month = $this->FilterMonth;
        $year = $this->FilterYear;

        $StartOfMonthDefault = Carbon::create($year, $month, 1)->startOfMonth();
        $StartOfMonth = Carbon::create($year, $month, 1)->startOfMonth();

        $EndOfMonth = Carbon::create($year, $month, 1)->endOfMonth();

        $MonthDays = [];
        $ViewVideoChartData = [];
        $SubscribersChartData = [];
        $BenefitChartData = [];
        $BenefitCreditChartData = [];
        $PremiumChartData = [];
        $CreditChartData = [];
        $PaidReferralChartData = [];
        $ReferralChartData = [];


        for ($date = $StartOfMonth; $date->lte($EndOfMonth); $date->addDay()) {
            $StartOfDay = $date->copy()->startOfDay();
            $EndOfDay = $date->copy()->endOfDay();
            $DayWithoutHours=$date->format('Y-m-d');
            $MonthDays[] = $date->day; // Add each day in YYYY-MM-DD format

            $totalViewOfVideo=TotalVideoView::Where('date', $DayWithoutHours )->first();

            if ($totalViewOfVideo) {
                $ViewVideoChartData[] = $totalViewOfVideo->view_count;
            }else{
                $ViewVideoChartData[] = 0;
            }

            $SubscribersChartData[] = User::
//            whereHas('roles', function ($query) {
//                    $query->where('id', '2');
//                })->
                  whereBetween('created_at', [$StartOfDay,$EndOfDay ])
                ->count();

            $BenefitChartData[] = PremiumToUsers::with('promoCode')
                ->whereBetween('created_at', [$StartOfDay, $EndOfDay])
                ->get()
                ->sum(function ($item) {
                    if ($item->promoCode) {
                        return $item->price / 100 * (100 - $item->promoCode->discount);
                    }

                    return $item->price;
                });

            $BenefitCreditChartData[] = BalanceEdit::with('promoCode')->where('type','!=',2)->where('user_id','!=',30436)
                ->whereBetween('created_at', [$StartOfDay, $EndOfDay])
                ->get()
                ->sum(function ($item) {
                    if ($item->promoCode) {
                        return $item->price / 100 * (100 - $item->promoCode->discount);
                    }

                    return $item->price;
                });


            $PaidReferralChartData[]=User::whereBetween('referral_expired_at',[$StartOfDay,$EndOfDay])
                ->where('created_at', '>', $StartOfMonthDefault)
                ->with('firstPremium')
                ->get()
                ->sum(function ($user)  {
                    if ($user->firstPremium) {
                        return ($user->firstPremium->price / 100 * (100 - $user->firstPremium->discount) / 100 *  ($user->current_referral_percentage ?? 50)) ;
                    }

                    return 0;
                });
            $ReferralChartData[]=User::whereBetween('created_at',[$StartOfDay,$EndOfDay])
                ->whereNull('referral_expired_at')
                ->with('firstPremium')
                ->where('created_by_id','!=',1)
                ->whereNotNull('created_by_id')
                ->get()
                ->sum(function ($user)  {
                    if ($user->firstPremium) {
                        return ($user->firstPremium->price / 100 * (100 - $user->firstPremium->discount) / 100 *  ($user->current_referral_percentage ?? 50)) ;
                    }

                    return 0;
                });



            $PremiumChartData[]=PremiumToUsers::whereBetween('created_at', [$StartOfDay,$EndOfDay ])
                ->count();
            $CreditChartData[]=BalanceEdit::whereBetween('created_at', [$StartOfDay,$EndOfDay ])
                ->count();

        }

        $this->allPremiumSum=number_format(array_sum($BenefitChartData),0,'.',' ');
        $this->allCreditSum=number_format(array_sum($BenefitCreditChartData),0,'.',' ');
        $this->allPaidReferralSum=number_format(array_sum($PaidReferralChartData),0,'.',' ');
        $this->allReferralSum=number_format(array_sum($ReferralChartData),0,'.',' ');

        $this->dispatch('ReloadVideoViewChart',[$MonthDays,$ViewVideoChartData]);
        $this->dispatch('SubscribersViewChart',[$MonthDays,$SubscribersChartData]);
        $this->dispatch('BenefitPriceChart',[$MonthDays,$BenefitChartData]);
        $this->dispatch('PremiumUsersChart',[$MonthDays,$PremiumChartData]);
        $this->dispatch('BenefitCreditPriceChart',[$MonthDays,$BenefitCreditChartData]);
        $this->dispatch('CreditUsersChart',[$MonthDays,$CreditChartData]);
        $this->dispatch('PaidReferralChartData',[$MonthDays,$PaidReferralChartData]);
        $this->dispatch('ReferralChartData',[$MonthDays,$ReferralChartData]);
    }
    public function Generate_Video_List()
    {
        $mostViewedVideo = Video::withCount('watcheds')
            ->orderBy('view_count', 'desc')
            ->take(10)
            ->get();

        $mostViewedSave = Video::withCount('savedLesson as saveds_lessons_count')
            ->orderBy('saveds_lessons_count', 'desc')
            ->take(10)
            ->get();

        $this->mostViewedVideo=$mostViewedVideo;
        $this->mostViewedSave=$mostViewedSave;
    }

    public function generate_user_details()
    {
        $this->UserCount=User::with('roles')
            ->whereHas('roles', function ($query) {
            $query->where('id', '2');
            })->where('mobile_verified_at','!=',null)
            ->count();

        $month = $this->FilterMonth;
        $year = $this->FilterYear;

        $StartOfMonth = Carbon::create($year, $month, 1)->startOfMonth();

        $EndOfMonth = Carbon::create($year, $month, 1)->endOfMonth();

        $this->OneMonthUserCount = User::with('roles')
            ->whereBetween('created_at', [$StartOfMonth,$EndOfMonth ])
//            ->whereHas('roles', function ($query) {
//                $query->where('id', '2');
//            })
            ->count();

        $StartOfYear = Carbon::create($year, 1, 1)->startOfYear();

        $EndOfYear = Carbon::create($year, 1, 1)->endOfYear();

        $this->OneYearUserCount = User::with('roles')
            ->whereBetween('mobile_verified_at', [$StartOfYear, $EndOfYear])
            ->whereHas('roles', function ($query) {
                $query->where('id', '2');
            })->count();
    }

    public function render()
    {
        return view('livewire.admin.dashboard.dashboard-index-livewire');
    }
}
