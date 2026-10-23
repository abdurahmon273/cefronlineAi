<?php

namespace App\Livewire\Admin\Reports;

use App\Enums\CefrTypeEnum;
use App\Models\WritingResult;
use Livewire\Component;
use ZipArchive;

class GetWritingResultsLivewire extends Component
{
    public $start_date;
    public $end_date;
    public $count = 100;

    // Bitta Word faylga nechta writing result joylansin
    public $counts = [100, 200, 300, 400, 500, 1000];

    // Part category: all | 1 (Task 1) | 2 (Task 2) | 3 (Full) — default Full
    public $category = '3';

    public $categories = [
        'all' => 'All',
        '1'   => 'Task 1',
        '2'   => 'Task 2',
        '3'   => 'Full writing',
    ];

    protected $listeners = [
        'updated_writing_start_date',
        'updated_writing_end_date',
    ];

    public function updated_writing_start_date($date)
    {
        $this->start_date = $date;
    }

    public function updated_writing_end_date($date)
    {
        $this->end_date = $date;
    }

    public function download()
    {
        $perFile = (int) $this->count;
        if ($perFile <= 0) {
            session()->flash('writing_error', 'Iltimos, to\'g\'ri count tanlang.');
            return;
        }

        $query = WritingResult::query()
            ->with(['partOneAnswer', 'partTwoAnswer', 'exercise.task_one', 'exercise.task_two'])
            ->where('status', 3)
            ->when($this->start_date, fn ($q) => $q->where('created_at', '>=', $this->start_date))
            ->when($this->end_date, fn ($q) => $q->where('created_at', '<=', $this->end_date))
            ->when($this->category !== 'all', fn ($q) => $q->where('category_type', $this->category))
            ->orderBy('id');

        if (!$query->clone()->exists()) {
            session()->flash('writing_error', 'Tanlangan oraliqda tekshirilgan writing topilmadi.');
            return;
        }

        $stamp = now()->format('Y_m_d_H_i');
        $zipPath = storage_path('app/writing_results_' . $stamp . '_' . uniqid() . '.zip');

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            session()->flash('writing_error', 'ZIP fayl yaratib bo\'lmadi.');
            return;
        }

        $part = 0;
        $offset = 0;

        // Har $perFile tadan alohida Word fayl qilib ZIP ichiga qo'shamiz
        $query->chunk($perFile, function ($results) use ($zip, &$part, &$offset, $stamp) {
            $part++;

            $from = $offset + 1;
            $to   = $offset + $results->count();

            $zip->addFromString(
                'writing_results_' . $stamp . '_part_' . str_pad($part, 3, '0', STR_PAD_LEFT)
                    . '_' . $from . '-' . $to . '.doc',
                $this->buildDocHtml($results, $offset)
            );

            $offset = $to;
        });

        $zip->close();

        return response()->download($zipPath, 'writing_results_' . $stamp . '.zip')
            ->deleteFileAfterSend(true);
    }

    private function buildDocHtml($results, int $startIndex = 0): string
    {
        $line = str_repeat('═', 40);
        $thin = str_repeat('─', 40);

        $blocks = '';
        $index = $startIndex;

        foreach ($results as $result) {
            $index++;
            $num = str_pad($index, 3, '0', STR_PAD_LEFT);

            $categoryType = (int) $result->category_type;
            $typeLabel = WritingResult::CATEGORY_TYPES[$categoryType] ?? '-';

            $p1 = $result->partOneAnswer;
            $p2 = $result->partTwoAnswer;

            $exercise = $result->exercise;
            $taskOne = $exercise?->task_one;
            $taskTwo = $exercise?->task_two;

            $score1 = $p1 ? floatval($p1->score_1) : 0;
            $score2 = $p1 ? floatval($p1->score_2) : 0;
            $scoreT2 = $p2 ? floatval($p2->score) : 0;

            $lines = [];
            $lines[] = $line;
            $lines[] = "WRITING #{$num}";
            $lines[] = $line;
            $lines[] = "ID       : 000{$result->id}";
            $lines[] = "Type     : {$typeLabel}";
            $lines[] = '';

            // Task 1 (1.1 / 1.2) — Task 1 va Full uchun
            if ($categoryType === 1 || $categoryType === 3) {
                $lines[] = 'Task 1.1 degree score : ' . $this->fmt($score1) . ' / ' . CefrTypeEnum::WRITING_ADDITIONAL_MAX_SCORES[1];
                $lines[] = 'Task 1.2 degree score : ' . $this->fmt($score2) . ' / ' . CefrTypeEnum::WRITING_ADDITIONAL_MAX_SCORES[2];
            }

            // Task 2 — Task 2 va Full uchun
            if ($categoryType === 2 || $categoryType === 3) {
                $lines[] = 'Task 2   degree score : ' . $this->fmt($scoreT2) . ' / ' . CefrTypeEnum::WRITING_ADDITIONAL_MAX_SCORES[3];
            }

            $lines[] = '';

            // Umumiy score va ball
            $totalScore = $categoryType === 1
                ? $score1 + $score2
                : ($categoryType === 2 ? $scoreT2 : $score1 + $score2 + $scoreT2);

            $ball = (int) (CefrTypeEnum::WRITING_BALL[$categoryType][$this->fmt($totalScore)] ?? 0);
            $maxBall = CefrTypeEnum::WRITING_MAX_SCORES[$categoryType] ?? 75;
            $level = $this->generateLevel($ball);

            $lines[] = 'All Score : ' . $this->fmt($totalScore);
            $lines[] = "Ball      : {$ball} / {$maxBall}";
            $lines[] = "Level     : {$level}";
            $lines[] = '';

            // Task 1 — savol va javoblar (Task 1 va Full uchun)
            if ($categoryType === 1 || $categoryType === 3) {
                $lines[] = $thin;
                $lines[] = 'TASK 1 - QUESTION';
                $lines[] = $this->clean($taskOne?->theme);
                $lines[] = '';
                $lines[] = 'TASK 1.1 - ANSWER';
                $lines[] = $this->clean($p1?->text_1);
                $lines[] = '';
                $lines[] = 'TASK 1.2 - ANSWER';
                $lines[] = $this->clean($p1?->text_2);
                $lines[] = '';
            }

            // Task 2 — savol va javob (Task 2 va Full uchun)
            if ($categoryType === 2 || $categoryType === 3) {
                $lines[] = $thin;
                $lines[] = 'TASK 2 - QUESTION';
                $lines[] = $this->clean($taskTwo?->theme);
                $lines[] = '';
                $lines[] = 'TASK 2 - ANSWER';
                $lines[] = $this->clean($p2?->text);
                $lines[] = '';
            }

            $lines[] = $thin;
            $lines[] = 'REAL EXAMINER';
            $lines[] = 'Ball  : ';
            $lines[] = 'Level : ';
            $lines[] = '';
            $lines[] = '';

            $blocks .= e(implode("\n", $lines)) . "\n";
        }

        return '<html xmlns:o="urn:schemas-microsoft-com:office:office" '
            . 'xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">'
            . '<head><meta charset="utf-8"></head><body>'
            . '<pre style="font-family:\'Courier New\',monospace; font-size:11pt;">'
            . $blocks
            . '</pre></body></html>';
    }

    private function clean($text): string
    {
        if (blank($text)) {
            return '-';
        }

        // Agar HTML bo'lsa teglarni olib tashlaymiz
        $text = strip_tags($text);
        // Correction token'lari {to'g'ri} va [xato] — faqat ichidagi matn qoladi
        $text = preg_replace('/\{([^}]*)\}/u', '$1', $text);
        $text = preg_replace('/\[([^\]]*)\]/u', '$1', $text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim($text);
    }

    private function fmt($value): string
    {
        return rtrim(rtrim(number_format((float) $value, 1, '.', ''), '0'), '.') ?: '0';
    }

    private function generateLevel($ball): string
    {
        $ball = (int) $ball;
        if ($ball < 0 || $ball > 75) {
            return 'Invalid score';
        }
        if ($ball <= 37) {
            return 'Below B1';
        } elseif ($ball <= 50) {
            return 'B1';
        } elseif ($ball <= 64) {
            return 'B2';
        }
        return 'C1';
    }

    public function render()
    {
        return view('livewire.admin.reports.get-writing-results-livewire');
    }
}
