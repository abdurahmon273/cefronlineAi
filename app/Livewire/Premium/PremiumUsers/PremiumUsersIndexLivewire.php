<?php

namespace App\Livewire\Premium\PremiumUsers;

use App\Models\Plan;
use App\Models\PremiumToUsers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PremiumUsersExport;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class PremiumUsersIndexLivewire extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $start_date, $end_date;
    public $search = '';
    public $type;
    public $paginate = 50;
    public $plans=[],$plan_id;
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
            new PremiumUsersExport($this->search, $this->type, $this->start_date, $this->end_date,$this->plan_id),
            'premium_users.xlsx'
        );
    }

    public function metaExport()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['ID', 'Name', 'Surname', 'Phone Number', 'Email', 'Birth Date', 'Added Date'];
        foreach ($headers as $index => $header) {
            $sheet->setCellValueByColumnAndRow($index + 1, 1, $header);
        }

        $row = 2;
        $chunkSize = 2000;

        PremiumToUsers::query()
            ->whereHas('user')
            ->whereHas('user', function ($query) {
                $query->when($this->search, function ($users) {
                    $search = strtolower($this->search);
                    $users->where(function ($subQuery) use ($search) {
                        $subQuery->orWhereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('LOWER(surname) LIKE ?', ["%{$search}%"])
                            ->orWhere('mobil_number', 'like', "%{$search}%")
                            ->orWhere('email', 'like', '%' . $this->search . '%');
                    });
                });
            })
            ->when($this->start_date, function ($query) {
                $query->where('created_at', '>=', $this->start_date);
            })
            ->when($this->end_date, function ($query) {
                $query->where('created_at', '<=', $this->end_date);
            })
            ->when(($this->type == '0' || $this->type == '2'), function ($query) {
                $query->where('type', $this->type);
            })
            ->when($this->type && $this->type !== '0' && !$this->type == '2', function ($query) {
                $query->where('admin_id', $this->type);
            })
            ->when($this->plan_id, function ($query) {
                $query->where('plan_id', $this->plan_id);
            })
            ->with(['user' => function ($query) {
                $query->select('id', 'name', 'surname', 'mobil_number', 'email', 'birth_date', 'created_at');
            }])
            ->select(['id', 'user_id'])
            ->orderByDesc('id')
            ->chunk($chunkSize, function ($premium_users) use ($sheet, &$row) {
                foreach ($premium_users as $premium) {
                    $user = $premium->user;
                    if (!$user) {
                        continue;
                    }
                    $sheet->setCellValueByColumnAndRow(1, $row, $user->id);
                    $sheet->setCellValueByColumnAndRow(2, $row, $user->name);
                    $sheet->setCellValueByColumnAndRow(3, $row, $user->surname);
                    $sheet->setCellValueByColumnAndRow(4, $row, $user->mobil_number);
                    $sheet->setCellValueByColumnAndRow(5, $row, $user->email);
                    $sheet->setCellValueByColumnAndRow(6, $row, $user->birth_date);
                    $sheet->setCellValueByColumnAndRow(7, $row, $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('Y-m-d H:i') : '');
                    $row++;
                }
            });

        $filename = 'meta_premium_users_' . now()->format('Y_m_d_His') . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function mount()
    {
      $this->plans=Plan::pluck('name','id');
      $this->extra_admins=User::whereHas('roles', function ($query) {
                $query->where('title', 'extra_admin');
            })->pluck('name','id');

    }
    public function render()
    {
        $query=PremiumToUsers::with(['user','plan','promoCode','admin'=>function($query){
            $query->select('id','name');
        }])
            ->whereHas('user')
            ->whereHas('user',function ($query){
                $query->when($this->search, function ($users) {
                        $search = strtolower($this->search); // Katta-kichik harfni farqlamaslik uchun
                        $users->where(function ($subQuery) use ($search) {
                            $subQuery->orWhereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                                ->orWhereRaw('LOWER(surname) LIKE ?', ["%{$search}%"])
                                ->orWhere('mobil_number', 'like', "%{$search}%")
                                ->orWhere('email', 'like', '%' . $this->search . '%');// Telefon raqami bo‘yicha qidirish
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
            ->orderByDesc('id');

        $premium_users=$query->paginate($this->paginate);

        $filteredSum = $query->sum('price');


        return view('livewire.premium.premium-users.premium-users-index-livewire',compact('premium_users','filteredSum'));
    }
}
