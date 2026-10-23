<?php

namespace App\Livewire\Admin\UserMenagement;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class IndexLivewire extends Component
{
    use WithPagination;

    public $perPage = 100;
    public $search = '';
    public $exporting = false;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        'search_users'
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function search_users($search)
    {
        $this->search = $search;
        $this->render();
    }

    public function exportExcel()
    {
        $this->exporting = true;

        $search = $this->search;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['ID', 'Name', 'Surname', 'Phone Number', 'Email', 'Birth Date', 'Added Date'];
        foreach ($headers as $index => $header) {
            $sheet->setCellValueByColumnAndRow($index + 1, 1, $header);
        }

        $row = 2;
        $chunkSize = 2000;

        User::query()
            ->when($search, function ($query) use ($search) {
                $s = strtolower($search);
                $query->where(function ($sub) use ($s, $search) {
                    $sub->orWhereRaw('LOWER(name) LIKE ?', ["%{$s}%"])
                        ->orWhereRaw('LOWER(surname) LIKE ?', ["%{$s}%"])
                        ->orWhere('mobil_number', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->select(['id', 'name', 'surname', 'mobil_number', 'email', 'birth_date', 'created_at'])
            ->chunk($chunkSize, function ($users) use ($sheet, &$row) {
                foreach ($users as $user) {
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

        $this->exporting = false;

        $filename = 'users_' . now()->format('Y_m_d_His') . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function render()
    {
        $users = User::query()
            ->with(['roles', 'city.region'])
            ->when($this->search, function ($query) {
                $search = strtolower($this->search);
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->orWhereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(surname) LIKE ?', ["%{$search}%"])
                        ->orWhere('mobil_number', 'like', "%{$search}%")
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        return view('livewire.admin.user-menagement.index-livewire', compact('users'));
    }
}
