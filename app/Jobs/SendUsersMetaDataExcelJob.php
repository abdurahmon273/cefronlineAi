<?php

namespace App\Jobs;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer as XlsxWriter;

class SendUsersMetaDataExcelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 600;

    // ====================================================================
    //  👉 BU YERGA o'z bot token va chat id laringizni qo'lda yozing.
    //  (Livewire ham shularni yuborishi mumkin — agar yuborilsa, o'shasi
    //   ishlatiladi, yuborilmasa quyidagi qiymatlar ishlatiladi.)
    // ====================================================================
    private string $botToken = '';   // masalan: '123456:ABC-DEF...'
    private string $chatId1  = '';   // 1-chat id
    private string $chatId2  = '';   // 2-chat id

    private ?string $startDate;
    private ?string $endDate;
    private int $count;
    private int $page;
    private int $totalPages;

    public function __construct(
        ?string $startDate,
        ?string $endDate,
        int $count,
        int $page,
        int $totalPages,
        string $botToken = '',
        string $chatId1 = '',
        string $chatId2 = ''
    ) {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->count = $count;
        $this->page = $page;
        $this->totalPages = $totalPages;

        // Livewire'dan kelgan qiymatlar bo'sh bo'lmasa — ularni ishlatamiz.
        if ($botToken !== '') $this->botToken = $botToken;
        if ($chatId1 !== '')  $this->chatId1 = $chatId1;
        if ($chatId2 !== '')  $this->chatId2 = $chatId2;
    }

    public function handle(): void
    {
        $offset = ($this->page - 1) * $this->count;

        $users = User::query()
            ->when($this->startDate, fn ($q) => $q->where('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn ($q) => $q->where('created_at', '<=', $this->endDate))
            ->orderBy('id')
            ->select(['id', 'name', 'surname', 'mobil_number', 'email', 'birth_date', 'created_at'])
            ->offset($offset)
            ->limit($this->count)
            ->get();

        if ($users->isEmpty()) {
            return;
        }

        // --- Excel faylni vaqtinchalik diskka yozamiz (openspout, past xotira) ---
        $path = storage_path('app/users_meta_' . $this->page . '_of_' . $this->totalPages . '_' . uniqid() . '.xlsx');

        $writer = new XlsxWriter();
        $writer->openToFile($path);
        $writer->addRow(Row::fromValues(
            ['№', 'ID', 'Name', 'Surname', 'Phone Number', 'Email', 'Birth Date', 'Added Date']
        ));

        $number = $offset + 1; // global tartib raqami
        foreach ($users as $user) {
            $writer->addRow(Row::fromValues([
                $number,
                $user->id,
                $user->name,
                $user->surname,
                $user->mobil_number,
                $user->email,
                $user->birth_date,
                $user->created_at ? Carbon::parse($user->created_at)->format('Y-m-d H:i') : '',
            ]));
            $number++;
        }
        $writer->close();

        // --- Telegram orqali 2 ta chat id ga yuboramiz ---
        $caption = $this->page . '/' . $this->totalPages;
        $filename = 'users_meta_' . $this->page . '_of_' . $this->totalPages . '.xlsx';

        foreach ([$this->chatId1, $this->chatId2] as $chatId) {
            if (empty($chatId)) {
                continue;
            }

            Http::timeout(120)
                ->attach('document', file_get_contents($path), $filename)
                ->post("https://api.telegram.org/bot{$this->botToken}/sendDocument", [
                    'chat_id' => $chatId,
                    'caption' => $caption,
                ]);
        }

        @unlink($path);
    }
}
