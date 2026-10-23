<?php

namespace App\Livewire\Admin\Reports;

use App\Jobs\SendUsersMetaDataExcelJob;
use App\Models\User;
use Livewire\Component;

class GetUsersMetaDataLivewire extends Component
{
    // ====================================================================
    //  👉 BU YERGA o'z bot token va chat id laringizni qo'lda yozing.
    // ====================================================================
    private string $botToken = '8929069859:AAG9a2k5NrqYEbq-hcph4XTl2RUgia_u2Lo';   // masalan: '123456:ABC-DEF...'
    private string $chatId1  = '5487863592';   // 1-chat id
    private string $chatId2  = '166592957';   // 2-chat id

    public $start_date;
    public $end_date;
    public $count = 500;

    // select uchun variantlar
    public $counts = [500, 1000, 2000, 3000];

    protected $listeners = [
        'updated_start_date',
        'updated_end_date',
    ];

    public function updated_start_date($date)
    {
        $this->start_date = $date;
    }

    public function updated_end_date($date)
    {
        $this->end_date = $date;
    }

    public function send()
    {
        $count = (int) $this->count;
        if ($count <= 0) {
            session()->flash('error', 'Iltimos, to\'g\'ri count tanlang.');
            return;
        }

        // Tanlangan sana oralig'idagi (created_at) userlar soni
        $total = User::query()
            ->when($this->start_date, fn ($q) => $q->where('created_at', '>=', $this->start_date))
            ->when($this->end_date, fn ($q) => $q->where('created_at', '<=', $this->end_date))
            ->count();

        if ($total === 0) {
            session()->flash('error', 'Tanlangan oraliqda user topilmadi.');
            return;
        }

        // Nechta fayl / job kerak (masalan 16000 / 2000 = 8)
        $totalPages = (int) ceil($total / $count);

        for ($page = 1; $page <= $totalPages; $page++) {
            SendUsersMetaDataExcelJob::dispatch(
                $this->start_date,
                $this->end_date,
                $count,
                $page,
                $totalPages,
                $this->botToken,
                $this->chatId1,
                $this->chatId2,
            );
        }

        session()->flash('message', "{$total} ta user uchun {$totalPages} ta fayl tayyorlanib, Telegram'ga yuborilmoqda.");
    }

    public function render()
    {
        return view('livewire.admin.reports.get-users-meta-data-livewire');
    }
}
