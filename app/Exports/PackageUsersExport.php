<?php

namespace App\Exports;

use App\Models\BalanceEdit;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PackageUsersExport implements FromView
{
    protected $search;
    protected $type;
    protected $start_date;
    protected $end_date;
    protected $package_id;

    public function __construct($search, $type, $start_date, $end_date, $package_id)
    {
        $this->search = $search;
        $this->type = $type;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->package_id = $package_id;
    }

    public function view(): View
    {
        $package_users = BalanceEdit::with([
            'user',
            'package',
            'admin' => function ($query) {
                $query->select('id', 'name');
            }
        ])
            ->whereHas('user')
            ->whereHas('user', function ($query) {
                $query->when($this->search, function ($users) {
                    $search = strtolower($this->search);

                    $users->where(function ($subQuery) use ($search) {
                        $subQuery->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
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
            ->when($this->type === '0', function ($query) {
                $query->where('type', '0');
            })
            ->when($this->type !== null && $this->type !== '' && $this->type !== '0', function ($query) {
                $query->where('admin_id', $this->type);
            })
            ->when($this->package_id, function ($query) {
                $query->where('package_id', $this->package_id);
            })
            ->orderByDesc('id')
            ->get();

        $filteredSum = (clone $package_users)->sum('price');
        $filteredUSDSum = (clone $package_users)->sum('value');

        return view('exports.package_users', compact('package_users', 'filteredSum','filteredUSDSum'));
    }
}
