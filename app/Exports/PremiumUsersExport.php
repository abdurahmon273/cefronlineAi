<?php
namespace App\Exports;

use App\Models\PremiumToUsers;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PremiumUsersExport implements FromView
{
    protected $search, $type, $start_date, $end_date,$plan_id;

    public function __construct($search, $type, $start_date, $end_date,$plan_id)
    {
        $this->search = $search;
        $this->type = $type;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->plan_id = $plan_id;


    }

    public function view(): View
    {
        $premium_users = PremiumToUsers::with(['user','plan','promoCode','admin'=>function($query){
            $query->select('id','name');
        }])
            ->whereHas('user')
            ->whereHas('user', function ($query) {
                $query->when($this->search, function ($users) {
                        $search = strtolower($this->search);
                        $users->where(function ($subQuery) use ($search) {
                            $subQuery->orWhereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                                ->orWhereRaw('LOWER(surname) LIKE ?', ["%{$search}%"])
                                ->orWhere('mobil_number', 'like', "%{$search}%");
                        });
                    });
            })
            ->when($this->start_date, function ($query) {
                $query->where('created_at', '>=', $this->start_date);
            })
            ->when($this->end_date, function ($query) {
                $query->where('created_at', '<=', $this->end_date);
            })
            ->when(($this->type=='0' || $this->type=='2') , function ($query) {
                $query->where('type', $this->type);
            })
            ->when($this->type && $this->type!=='0' && !$this->type=='2' , function ($query) {
                $query->where('admin_id', $this->type);
            })
            ->when($this->plan_id , function ($query) {
                $query->where('plan_id', $this->plan_id);
            })
            ->orderByDesc('id')
            ->get();

        return view('exports.premium_users', compact('premium_users'));
    }
}

