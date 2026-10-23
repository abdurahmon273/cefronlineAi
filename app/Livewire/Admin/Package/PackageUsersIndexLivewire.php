<?php

namespace App\Livewire\Admin\Package;

use App\Exports\PackageUsersExport;
use App\Exports\PremiumUsersExport;
use App\Models\BalanceEdit;
use App\Models\Package;
use App\Models\Plan;
use App\Models\PremiumToUsers;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class PackageUsersIndexLivewire extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $start_date, $end_date;
    public $search = '';
    public $type;
    public $paginate = 50;
    public $packages=[],$package_id;
    public $extra_admins=[];
    public $showFilters = false;

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
            new PackageUsersExport(
                $this->search,
                $this->type,
                $this->start_date,
                $this->end_date,
                $this->package_id
            ),
            'package_users.xlsx'
        );
    }

    public function mount()
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

        $this->extra_admins=User::whereHas('roles', function ($query) {
            $query->where('title', 'extra_admin');
        })->pluck('name','id');

    }
    public function render()
    {
        $query=BalanceEdit::with(['user','package','admin'=>function($query){
            $query->select('id','name');
        },'promoCode'=>function($query){
            $query->select('id','code','discount');
        }])
            ->whereHas('user')
            ->whereHas('user',function ($query){
                $query->when($this->search, function ($users) {
                    $search = strtolower($this->search); // Katta-kichik harfni farqlamaslik uchun
                    $users->where(function ($subQuery) use ($search) {
                        $subQuery->orWhereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('LOWER(surname) LIKE ?', ["%{$search}%"])
                            ->orWhere('mobil_number', 'like', "%{$search}%"); // Telefon raqami bo‘yicha qidirish
                    });
                });
            })
            ->when($this->start_date, function ($query) {
                $query->where('created_at', '>=', $this->start_date);
            })
            ->when($this->end_date, function ($query) {
                $query->where('created_at', '<=', $this->end_date);
            })
            ->when(($this->type=='0' || $this->type=='2'), function ($query) {
                $query->where('type', $this->type);
            })
            ->when($this->type && $this->type!=='0' && $this->type!=='2' , function ($query) {
                $query->where('admin_id', $this->type);
            })
            ->when($this->package_id , function ($query) {
                $query->where('package_id', $this->package_id);
            })
            ->orderByDesc('id');

        $package_users=$query->paginate($this->paginate);

        $filteredSum = $query->sum('price');


        return view('livewire.admin.package.package-users-index-livewire',compact('package_users','filteredSum'));
    }
}
