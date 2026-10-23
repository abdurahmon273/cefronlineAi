<?php

namespace App\Livewire\Admin\Reports;

use App\Enums\CefrTypeEnum;
use App\Models\SpeakingResult;
use Livewire\Component;
use ZipArchive;

class GetSpeakingResultsLivewire extends Component
{
    public $start_date;
    public $end_date;
    public $count = 100;

    // Bitta Word faylga nechta speaking result joylansin
    public $counts = [100, 200, 300, 400, 500, 1000];

    // Part category: all | 1 (Part 1) | 2 (Part 2) | 3 (Part 3) | 4 (Full) — default Full
    public $category = '4';

    public $categories = [
        'all' => 'All',
        '1'   => 'Part 1',
        '2'   => 'Part 2',
        '3'   => 'Part 3',
        '4'   => 'Full speaking',
    ];

    protected $listeners = [
        'updated_speaking_start_date',
        'updated_speaking_end_date',
    ];

    public function updated_speaking_start_date($date)
    {
        $this->start_date = $date;
    }

    public function updated_speaking_end_date($date)
    {
        $this->end_date = $date;
    }

    public function download()
    {
        $perFile = (int) $this->count;
        if ($perFile <= 0) {
            session()->flash('speaking_error', 'Iltimos, to\'g\'ri count tanlang.');
            return;
        }

        $query = SpeakingResult::query()
            ->with([
                'part_one_first.answers',
                'part_one_second.answers',
                'part_two',
                'part_three',
                'exercise.part_one_first.questions',
                'exercise.part_one_second.questions',
                'exercise.part_two.question',
                'exercise.part_three',
            ])
            ->where('status', 3)
            ->when($this->start_date, fn ($q) => $q->where('created_at', '>=', $this->start_date))
            ->when($this->end_date, fn ($q) => $q->where('created_at', '<=', $this->end_date))
            ->when($this->category !== 'all', fn ($q) => $q->where('category_type', $this->category))
            ->orderBy('id');

        if (!$query->clone()->exists()) {
            session()->flash('speaking_error', 'Tanlangan oraliqda tekshirilgan speaking topilmadi.');
            return;
        }

        $stamp = now()->format('Y_m_d_H_i');
        $zipPath = storage_path('app/speaking_results_' . $stamp . '_' . uniqid() . '.zip');

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            session()->flash('speaking_error', 'ZIP fayl yaratib bo\'lmadi.');
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
                'speaking_results_' . $stamp . '_part_' . str_pad($part, 3, '0', STR_PAD_LEFT)
                    . '_' . $from . '-' . $to . '.doc',
                $this->buildDocHtml($results, $offset)
            );

            $offset = $to;
        });

        $zip->close();

        return response()->download($zipPath, 'speaking_results_' . $stamp . '.zip')
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
            $typeLabel = SpeakingResult::CATEGORY_TYPES[$categoryType] ?? '-';

            $exercise = $result->exercise;

            $p11 = $result->part_one_first;
            $p12 = $result->part_one_second;
            $p2  = $result->part_two;
            $p3  = $result->part_three;

            $score11 = $p11 ? floatval($p11->score) : 0;
            $score12 = $p12 ? floatval($p12->score) : 0;
            $score2  = $p2 ? floatval($p2->score) : 0;
            $score3  = $p3 ? floatval($p3->score) : 0;

            $lines = [];
            $lines[] = $line;
            $lines[] = "SPEAKING #{$num}";
            $lines[] = $line;
            $lines[] = "ID       : 000{$result->id}";
            $lines[] = "Type     : {$typeLabel}";
            $lines[] = '';

            // Part 1 (1.1 / 1.2) — Part 1 va Full uchun
            if ($categoryType === 1 || $categoryType === 4) {
                $lines[] = 'Part 1.1 degree score : ' . $this->fmt($score11) . ' / ' . CefrTypeEnum::SPEAKING_ADDITIONAL_MAX_SCORES[1];
                $lines[] = 'Part 1.2 degree score : ' . $this->fmt($score12) . ' / ' . CefrTypeEnum::SPEAKING_ADDITIONAL_MAX_SCORES[2];
            }

            // Part 2 — Part 2 va Full uchun
            if ($categoryType === 2 || $categoryType === 4) {
                $lines[] = 'Part 2   degree score : ' . $this->fmt($score2) . ' / ' . CefrTypeEnum::SPEAKING_ADDITIONAL_MAX_SCORES[3];
            }

            // Part 3 — Part 3 va Full uchun
            if ($categoryType === 3 || $categoryType === 4) {
                $lines[] = 'Part 3   degree score : ' . $this->fmt($score3) . ' / ' . CefrTypeEnum::SPEAKING_ADDITIONAL_MAX_SCORES[4];
            }

            $lines[] = '';

            // Umumiy score va ball
            $totalScore = match ($categoryType) {
                1 => $score11 + $score12,
                2 => $score2,
                3 => $score3,
                default => floatval($result->score),
            };

            $ball = (int) (CefrTypeEnum::SPEAKING_BALL[$categoryType][$this->fmt($totalScore)] ?? 0);
            $maxBall = CefrTypeEnum::SPEAKING_MAX_SCORES[$categoryType] ?? 75;
            $level = $this->generateLevel($ball);

            $lines[] = 'All Score : ' . $this->fmt($totalScore);
            $lines[] = "Ball      : {$ball} / {$maxBall}";
            $lines[] = "Level     : {$level}";
            $lines[] = '';

            // Part 1 — savol va javoblar (Part 1 va Full uchun)
            if ($categoryType === 1 || $categoryType === 4) {
                $lines[] = $thin;
                $lines[] = 'PART 1.1 - QUESTIONS & ANSWERS';
                $lines = array_merge($lines, $this->questionAnswerLines(
                    $exercise?->part_one_first?->questions,
                    $p11?->answers
                ));
                $lines[] = '';

                $lines[] = $thin;
                $lines[] = 'PART 1.2 - QUESTIONS & ANSWERS';
                $lines = array_merge($lines, $this->questionAnswerLines(
                    $exercise?->part_one_second?->questions,
                    $p12?->answers
                ));
                $lines[] = '';
            }

            // Part 2 — savol va javob (Part 2 va Full uchun)
            if ($categoryType === 2 || $categoryType === 4) {
                $lines[] = $thin;
                $lines[] = 'PART 2 - QUESTION';
                $lines[] = $this->clean($exercise?->part_two?->question?->question);
                $lines[] = '';
                $lines[] = 'PART 2 - ANSWER';
                $lines[] = $this->clean($p2?->audio_text);
                $lines[] = '';
            }

            // Part 3 — savol va javob (Part 3 va Full uchun)
            if ($categoryType === 3 || $categoryType === 4) {
                $partThree = $exercise?->part_three;

                $lines[] = $thin;
                $lines[] = 'PART 3 - THEME';
                $lines[] = $this->clean($partThree?->theme);
                $lines[] = '';
                $lines[] = 'PART 3 - QUESTION';
                $lines[] = $this->clean($partThree?->question_text);
                $lines[] = '';
                $lines[] = 'PART 3 - ANSWER';
                $lines[] = $this->clean($p3?->audio_text);
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

    private function questionAnswerLines($questions, $answers): array
    {
        if (blank($questions)) {
            return ['-'];
        }

        $lines = [];
        $i = 0;

        foreach ($questions as $question) {
            $i++;
            $answer = $answers?->where('question_id', $question->id)->first();

            $lines[] = "Q{$i}: " . $this->clean($question->question);
            $lines[] = "A{$i}: " . $this->clean($answer?->audio_text);
            $lines[] = '';
        }

        return $lines;
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
        return view('livewire.admin.reports.get-speaking-results-livewire');
    }
}
