<?php

namespace App\Livewire\Premium\PromoCode;

use App\Exports\PromoCodeExport;
use App\Models\Package;
use App\Models\Plan;
use App\Models\PremiumToUsers;
use App\Models\Promocode;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class IndexLivewire extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $start_date, $end_date;
    public $search = '';
    public $type;
    public $paginate = 50;
    public $plans=[],$packages=[];
    public $plan_ids = [],$package_ids = [];
    public $showFilters = false;

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }
    protected $listeners=[
        'updated_start_date',
        'updated_end_date',
        'plans_changed' => 'onPlansChanged',
        'packages_changed' => 'onPackagesChanged',
    ];

    public function onPlansChanged($vals)
    {
        $this->plan_ids = array_map('intval', is_array($vals) ? $vals : (array)$vals);

        $this->resetPage();
    }
    public function onPackagesChanged($vals)
    {
        $this->package_ids = array_map('intval', is_array($vals) ? $vals : (array)$vals);

        $this->resetPage();
    }
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
            new PromoCodeExport($this->search, $this->type, $this->start_date, $this->end_date, $this->plan_ids),
            'promoCodes.xlsx'
        );

    }

    public function mount()
    {
        $this->plans=Plan::pluck('name','id');
        $this->packages=Package::pluck('price','id');
        $this->plan_ids = $this->plans->keys()->all();
        $this->package_ids = $this->packages->keys()->all();

    }
    public function render()
    {
        $query = Promocode::with(['plans' => function ($query) {
            $query->select('plans.id', 'plans.name');
        },'packages' => function ($query) {
            $query->select('packages.id', 'packages.price');
        }])
        ->when($this->search, function ($query) {
               return $query->where('code', 'like', '%'.$this->search.'%');
            })
        ->when($this->start_date, function ($query) {
                $query->where('end_date', '>=', $this->start_date);
            })
            ->when($this->end_date, function ($query) {
                $query->where('end_date', '<=', $this->end_date);
            })
            ->when($this->type, function ($query) {
                $query->where('type', $this->type);
            })
            ->when(!empty($this->plan_ids) || !empty($this->package_ids), function ($query) {
                $query->where(function ($subQuery) {
                    if (!empty($this->plan_ids) &&  empty($this->package_ids)) {
                        $subQuery->whereHas('plans', function ($planQuery) {
                            $planQuery->whereIn('plans.id', $this->plan_ids);
                        });
                    }

                    if (empty($this->plan_ids) &&  !empty($this->package_ids)) {
                        $subQuery->whereHas('packages', function ($packageQuery) {
                            $packageQuery->whereIn('packages.id', $this->plan_ids);
                        });
                    }



                });
            })
            ->orderByDesc('id');

        $premium_users=$query->paginate($this->paginate);

        $filteredSum = 0;


        return view('livewire.premium.promo-code.index-livewire',compact('premium_users','filteredSum'));
    }
}
