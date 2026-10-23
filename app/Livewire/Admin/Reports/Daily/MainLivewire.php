<?php

namespace App\Livewire\Admin\Reports\Daily;

use App\Models\FullResult;
use App\Models\ListeningResult;
use App\Models\ReadingResult;
use App\Models\SpeakingResult;
use App\Models\WritingResult;
use Carbon\Carbon;
use Livewire\Component;

class MainLivewire extends Component
{
    public $type = 0;
    public $startDate, $endDate;
    public $activeUsersCount = 0, $activeTime = '00:00', $averageSessionCount = 0;
    public $selectedYear, $selectedMonth, $selectedDay;
    public $availableYears = [], $availableMonths = [], $availableDays = [];
    public $examCounts = [
        'reading' => 0,
        'listening' => 0,
        'writing' => 0,
        'ai_writing' => 0,
        'plain_writing' => 0,
        'speaking' => 0,
        'ai_speaking' => 0,
        'plain_speaking' => 0,
        'full' => 0,
    ];

    protected function isMonthly(): bool
    {
        return (int) $this->type === 1;
    }

    public function getCountUsers($ReadingQuery, $ListeningQuery, $WritingQuery, $SpeakingQuery, $FullQuery)
    {
        $userIds = collect()
            ->merge(
                (clone $ReadingQuery)
                    ->whereBetween('created_at', [$this->startDate, $this->endDate])
                    ->whereNotNull('user_id')
                    ->distinct()
                    ->pluck('user_id')
            )
            ->merge(
                (clone $ListeningQuery)
                    ->whereBetween('created_at', [$this->startDate, $this->endDate])
                    ->whereNotNull('user_id')
                    ->distinct()
                    ->pluck('user_id')
            )
            ->merge(
                (clone $WritingQuery)
                    ->whereBetween('created_at', [$this->startDate, $this->endDate])
                    ->whereNotNull('user_id')
                    ->distinct()
                    ->pluck('user_id')
            )
            ->merge(
                (clone $SpeakingQuery)
                    ->whereBetween('created_at', [$this->startDate, $this->endDate])
                    ->whereNotNull('user_id')
                    ->distinct()
                    ->pluck('user_id')
            )
            ->merge(
                (clone $FullQuery)
                    ->whereBetween('created_at', [$this->startDate, $this->endDate])
                    ->whereNotNull('user_id')
                    ->distinct()
                    ->pluck('user_id')
            )
            ->filter()
            ->unique()
            ->values();

        $this->activeUsersCount = $userIds->count();
    }
    public function getAverageSessionCount($ReadingQuery, $ListeningQuery, $WritingQuery, $SpeakingQuery, $FullQuery)
    {

        $this->averageSessionCount = '-';
    }

    public function getActiveTime($ReadingQuery, $ListeningQuery, $WritingQuery, $SpeakingQuery, $FullQuery)
    {
        $queries = [
            $ReadingQuery,
            $ListeningQuery,
            $WritingQuery,
            $SpeakingQuery,
            $FullQuery,
        ];

        $hours = collect();

        foreach ($queries as $query) {
            $items = (clone $query)
                ->whereBetween('created_at', [$this->startDate, $this->endDate])
                ->selectRaw('HOUR(created_at) as hour, COUNT(*) as total')
                ->groupByRaw('HOUR(created_at)')
                ->pluck('total', 'hour');

            foreach ($items as $hour => $total) {
                $hours[$hour] = ($hours[$hour] ?? 0) + $total;
            }
        }

        if ($hours->isEmpty()) {
            $this->activeTime = '-';
            return;
        }

        $activeHour = $hours->sortDesc()->keys()->first();

        $this->activeTime = sprintf('%02d:00', $activeHour);
    }

    public function getExamsCount($ReadingQuery, $ListeningQuery, $WritingQuery, $SpeakingQuery, $FullQuery)
    {
        $readingCount = (clone $ReadingQuery)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->count();

        $listeningCount = (clone $ListeningQuery)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->count();

        $writingCount = (clone $WritingQuery)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->count();

        $aiWritingCount = (clone $WritingQuery)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('with_ai', 1)
            ->count();

        $speakingCount = (clone $SpeakingQuery)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->count();

        $aiSpeakingCount = (clone $SpeakingQuery)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('with_ai', 1)
            ->count();

        $fullCount = (clone $FullQuery)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->count();

        $this->examCounts = [
            'reading' => $readingCount,
            'listening' => $listeningCount,
            'writing' => $writingCount,
            'ai_writing' => $aiWritingCount,
            'plain_writing' => max($writingCount - $aiWritingCount, 0),
            'speaking' => $speakingCount,
            'ai_speaking' => $aiSpeakingCount,
            'plain_speaking' => max($speakingCount - $aiSpeakingCount, 0),
            'full' => $fullCount,
        ];
    }
    protected function buildMonths(): array
    {
        return [
            1 => 'Yanvar',
            2 => 'Fevral',
            3 => 'Mart',
            4 => 'Aprel',
            5 => 'May',
            6 => 'Iyun',
            7 => 'Iyul',
            8 => 'Avgust',
            9 => 'Sentyabr',
            10 => 'Oktyabr',
            11 => 'Noyabr',
            12 => 'Dekabr',
        ];
    }

    protected function refreshStats(): void
    {
        $ReadingQuery = ReadingResult::query();
        $ListeningQuery = ListeningResult::query();
        $WritingQuery = WritingResult::query();
        $SpeakingQuery = SpeakingResult::query();
        $FullQuery = FullResult::query();

        $this->getCountUsers($ReadingQuery, $ListeningQuery, $WritingQuery, $SpeakingQuery, $FullQuery);
        $this->getAverageSessionCount($ReadingQuery, $ListeningQuery, $WritingQuery, $SpeakingQuery, $FullQuery);
        $this->getActiveTime($ReadingQuery, $ListeningQuery, $WritingQuery, $SpeakingQuery, $FullQuery);

        $this->getExamsCount($ReadingQuery, $ListeningQuery, $WritingQuery, $SpeakingQuery, $FullQuery);
    }

    protected function syncAvailableDays(): void
    {
        $daysInMonth = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->daysInMonth;
        $this->availableDays = range(1, $daysInMonth);

        if ($this->selectedDay > $daysInMonth) {
            $this->selectedDay = $daysInMonth;
        }
    }

    public function mount($type = 0)
    {
        $today = Carbon::today();

        $this->type = $type;
        $this->availableYears = range($today->year - 2, $today->year);
        $this->availableMonths = $this->buildMonths();
        $this->selectedYear = $today->year;
        $this->selectedMonth = $today->month;
        $this->selectedDay = $today->day;
        $this->syncAvailableDays();

        $this->updatedDate();
    }

    public function updatedSelectedYear()
    {
        if (!$this->isMonthly()) {
            $this->syncAvailableDays();
        }

        $this->updatedDate();
    }

    public function updatedSelectedMonth()
    {
        if (!$this->isMonthly()) {
            $this->syncAvailableDays();
        }

        $this->updatedDate();
    }

    public function updatedSelectedDay()
    {
        $this->updatedDate();
    }

    public function updatedDate()
    {
        if ($this->isMonthly()) {
            $selectedDate = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1);
            $this->startDate = $selectedDate->copy()->startOfMonth()->startOfDay();
            $this->endDate = $selectedDate->copy()->endOfMonth()->endOfDay();
        } else {
            $selectedDate = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, $this->selectedDay);
            $this->startDate = $selectedDate->copy()->startOfDay();
            $this->endDate = $selectedDate->copy()->endOfDay();
        }

        $this->refreshStats();
    }

    public function setToday()
    {
        $today = Carbon::today();
        $this->selectedYear = $today->year;
        $this->selectedMonth = $today->month;

        if (!$this->isMonthly()) {
            $this->selectedDay = $today->day;
            $this->syncAvailableDays();
        }

        $this->updatedDate();
    }

    public function render()
    {
        return view('livewire.admin.reports.daily.main-livewire');
    }
}
