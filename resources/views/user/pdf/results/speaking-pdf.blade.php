<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Speaking Test Result PDF</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #333;
            line-height: 1.5;
            margin: 40px;
            background-color: #fff;
        }
        .header {
            border-bottom: 1px solid #ddd;
            padding-bottom: 15px;
            margin-bottom: 25px;
            text-align: center;
        }
        .header h1 {
            font-size: 20px;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #000;
        }
        .header .info {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }

        .summary {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            border: 1px solid #eee;
            border-radius: 4px;
        }
        .summary-cell {
            display: table-cell;
            padding: 15px;
            text-align: center;
            border-right: 1px solid #eee;
            vertical-align: middle;
        }
        .summary-cell:last-child { border-right: none; }

        .score-big {
            font-size: 22px;
            font-weight: bold;
            color: #ff9800;
        }
        .label-small {
            font-size: 10px;
            color: #888;
            text-transform: uppercase;
            display: block;
        }

        .section { margin-bottom: 30px; }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            border-left: 3px solid #ff9800;
            padding-left: 10px;
            margin-bottom: 15px;
            background: #f9f9f9;
            padding-top: 5px;
            padding-bottom: 5px;
        }

        .sub-section { margin-bottom: 20px; }
        .sub-title {
            font-size: 12px;
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
        }

        .text-box {
            font-size: 13px;
            color: #444;
            white-space: normal;
            border-left: 1px solid #eee;
            padding-left: 15px;
        }
        .text-box p { margin: 0 0 12px 0; }

        .qa {
            margin-bottom: 10px;
            padding: 10px;
            background: #fdfdfd;
            border: 1px solid #f0f0f0;
            border-radius: 4px;
        }
        .qa-q {
            font-size: 12px;
            font-weight: bold;
            color: #333;
            margin-bottom: 6px;
        }
        .qa-label {
            font-size: 11px;
            font-weight: bold;
            color: #666;
            display: block;
            margin-bottom: 2px;
        }

        .result-box{
            margin: 10px 0 14px 0;
            padding: 12px 14px;
            border: 1px solid #eee;
            border-radius: 6px;
            background: #fff;
        }
        .result-score{
            font-size: 16px;
            font-weight: bold;
            color: #111;
            margin: 0 0 6px 0;
        }
        .result-score span{
            color:#999;
            font-weight: 700;
        }
        .result-desc{
            font-size: 12px;
            color:#555;
            margin: 0;
            line-height: 1.6;
            white-space: normal;
        }

        .page-break { page-break-after: always; }

        /* --- Utility classes (Tailwind-like) for PDF --- */
        .mb-4 { margin-bottom: 16px; }
        .leading-7 { line-height: 1.75; }

        .text-green-600 { color: #16a34a; }
        .text-red-500 { color: #ef4444; }

        .underline { text-decoration: underline; }
        .line-through { text-decoration: line-through; }

        .font-medium { font-weight: 500; }
        .mx-1 { margin-left: 4px; margin-right: 4px; }

        /* images */
        .img-row { width: 100%; margin-top: 10px; }
        .img-row table { width: 100%; border-collapse: collapse; }
        .img-row td { width: 50%; padding: 6px; vertical-align: top; }
        .img-card {
            border: 1px solid #eee;
            border-radius: 6px;
            padding: 6px;
            background: #fafafa;
        }
        .img-card img {
            width: 100%;
            height: auto;
            display: block;
            border-radius: 4px;
        }
        .img-note { font-size: 11px; color: #999; }
    </style>
</head>
<body>

@php
    $isChecked = (int)($speakingResult->status ?? 0) === 3;
    $exerciseName = $speakingResult['exercise']['name'] ?? ($speakingResult->exercise->title ?? 'Practice');

    // ====== CONTENT FLAGS (bo'sh page-break chiqmasligi uchun) ======
    $hasPart1_1 = isset($part_one_first['questions']) && is_array($part_one_first['questions']) && count($part_one_first['questions']) > 0;
    $hasPart1_2 = isset($part_one_second) && (
        (isset($part_one_second['questions']) && is_array($part_one_second['questions']) && count($part_one_second['questions']) > 0)
        || (isset($part_one_second['images']) && is_array($part_one_second['images']) && count($part_one_second['images']) > 0)
    );
    $hasPart1 = $hasPart1_1 || $hasPart1_2;

    $hasPart2 = isset($part_two) && (
        !empty($part_two['question'])
        || (isset($part_two['images']) && is_array($part_two['images']) && count($part_two['images']) > 0)
        || !empty($part_two['resultData'])
    );

    $hasPart3 = isset($part_three) && (
        !empty($part_three['answer_text'])
        || !empty($part_three['theme'])
        || (isset($part_three['forAgainstData']) && (!empty($part_three['forAgainstData']['forDetails'] ?? '') || !empty($part_three['forAgainstData']['againtsDetails'] ?? '')))
        || !empty($part_three['resultData'])
    );
@endphp

<div class="header">
    <h1>Speaking Test Result</h1>
    <div class="info">
        ID: #000{{ $speakingResult->id ?? '' }} |
        Date: {{ $createdText ?? \Carbon\Carbon::now()->format('d.m.Y H:i') }} |
        Name: {{ $exerciseName }}
    </div>
</div>

<div class="summary">
    <div class="summary-cell">
        <span class="label-small">Accuracy</span>
        <span class="score-big">{{ $percentageData['percent'] ?? '0' }}%</span>
    </div>

    <div class="summary-cell">
        <span class="label-small">Score</span>
        <span class="score-big">
            {{ $speakingResult->percentageData['ball'] ?? '-' }}
            /
            {{ \App\Enums\CefrTypeEnum::SPEAKING_MAX_SCORES[$speakingResult->category_type] ?? '-' }}
        </span>
    </div>

    @if(isset($speakingResult->degree) && (int)($speakingResult->category_type ?? 0) === 4)
        <div class="summary-cell">
            <span class="label-small">Level</span>
            <span class="score-big">{{ $speakingResult->degree }}</span>
        </div>
    @endif

    <div class="summary-cell">
        <span class="label-small">Status</span>
        <span style="font-size: 14px; font-weight: bold;">
            {{ ($speakingResult->status==3) ? 'Checked' : \App\Enums\CefrTypeEnum::SPEAKING_STATUS_DATA[$speakingResult->status] }}
        </span>
    </div>

    @if(($speakingResult->category_type ?? null) == 4)
        @if(!empty($percentageData['degree']))
            <div class="summary-cell">
                <span class="label-small">Level</span>
                <span style="font-size: 14px; font-weight: bold;">
                    {{ $percentageData['degree'] }}
                </span>
            </div>
        @endif
    @endif
</div>

{{-- ===================== PART 1 ===================== --}}
@if($hasPart1)
    <div class="section">
        <div class="section-title">PART 1</div>

        {{-- Part 1.1 --}}
        @if($hasPart1_1)
            <div class="sub-section">
                <div class="sub-title">Part 1.1 | Questions</div>

                @if(!empty($part_one_first['resultData']))
                    <div class="result-box">
                        <div class="result-score">
                            {{ $part_one_first['resultData']['score'] ?? '-' }}
                            <span>/ {{ $part_one_first['resultData']['max_score'] ?? '-' }}</span>
                        </div>
                        <p class="result-desc">
                            {{ $part_one_first['resultData']['description'] ?? '' }}
                        </p>
                    </div>
                @endif

                @foreach($part_one_first['questions'] as $q)
                    <div class="qa">
                        <div class="qa-q">Q: {{ $q['question'] ?? '' }}</div>
                        <span class="qa-label">Answer:</span>
                        <div class="text-box">
                            {!! $q['answer_text'] ?? '—' !!}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Part 1.2 --}}
        @if($hasPart1_2)
            <div class="sub-section">
                <div class="sub-title">Part 1.2 | Questions</div>

                @if(!empty($part_one_second['resultData']))
                    <div class="result-box">
                        <div class="result-score">
                            {{ $part_one_second['resultData']['score'] ?? '-' }}
                            <span>/ {{ $part_one_second['resultData']['max_score'] ?? '-' }}</span>
                        </div>
                        <p class="result-desc">
                            {{ $part_one_second['resultData']['description'] ?? '' }}
                        </p>
                    </div>
                @endif

                {{-- images --}}
                @if(isset($part_one_second['images']) && is_array($part_one_second['images']) && count($part_one_second['images']))
                    <div class="img-row">
                        <table>
                            <tr>
                                @foreach($part_one_second['images'] as $image)
                                    @php
                                        $imgUrl = $image['url'] ?? '';
                                        $imgPath = $imgUrl ? public_path(ltrim($imgUrl, '/')) : '';
                                    @endphp
                                    <td>
                                        <div class="img-card">
                                            @if($imgPath && file_exists($imgPath))
                                                <img src="{{ $imgPath }}" alt="">
                                            @else
                                                <div class="img-note">Image not found</div>
                                            @endif
                                        </div>
                                    </td>
                                    @if($loop->iteration % 2 === 0 && !$loop->last)
                            </tr><tr>
                                @endif
                                @endforeach
                                @if(count($part_one_second['images']) % 2 === 1)
                                    <td></td>
                                @endif
                            </tr>
                        </table>
                    </div>
                @endif

                {{-- questions --}}
                @if(isset($part_one_second['questions']) && is_array($part_one_second['questions']) && count($part_one_second['questions']))
                    @foreach($part_one_second['questions'] as $q)
                        <div class="qa">
                            <div class="qa-q">Q: {{ $q['question'] ?? '' }}</div>
                            <span class="qa-label">Answer:</span>
                            <div class="text-box">
                                {!! $q['answer_text'] ?? '—' !!}
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        @endif
    </div>
@endif

{{-- PAGE BREAK: faqat keyingi part (2 yoki 3) bor bo'lsa --}}
@if($hasPart1 && ($hasPart2 || $hasPart3))
    <div class="page-break"></div>
@endif

{{-- ===================== PART 2 ===================== --}}
@if($hasPart2)
    <div class="section">

        <div class="section-title">PART 2</div>

        @if(!empty($part_two['resultData']))
            <div class="result-box">
                <div class="result-score">
                    {{ $part_two['resultData']['score'] ?? '-' }}
                    <span>/ {{ $part_two['resultData']['max_score'] ?? '-' }}</span>
                </div>
                <p class="result-desc">
                    {{ $part_two['resultData']['description'] ?? '' }}
                </p>
            </div>
        @endif

        @if(!empty($part_two['question']))
            <div class="sub-section">
                <div class="sub-title">Part 2 | Question</div>

                <div class="qa">
                    <div class="qa-q">Q: {{ $part_two['question']['question'] ?? '' }}</div>
                    <span class="qa-label">Answer:</span>
                    <div class="text-box">
                        {!! $part_two['question']['answer_text'] ?? '—' !!}
                    </div>
                </div>
            </div>
        @endif

        @if(isset($part_two['images']) && is_array($part_two['images']) && count($part_two['images']))
            <div class="sub-section">
                <div class="sub-title">Part 2 | Images</div>

                <div class="img-row">
                    <table>
                        <tr>
                            @foreach($part_two['images'] as $image)
                                @php
                                    $imgUrl = $image['url'] ?? '';
                                    $imgPath = $imgUrl ? public_path(ltrim($imgUrl, '/')) : '';
                                @endphp
                                <td>
                                    <div class="img-card">
                                        @if($imgPath && file_exists($imgPath))
                                            <img src="{{ $imgPath }}" alt="">
                                        @else
                                            <div class="img-note">Image not found</div>
                                        @endif
                                    </div>
                                </td>
                                @if($loop->iteration % 2 === 0 && !$loop->last)
                        </tr><tr>
                            @endif
                            @endforeach
                            @if(count($part_two['images']) % 2 === 1)
                                <td></td>
                            @endif
                        </tr>
                    </table>
                </div>
            </div>
        @endif

    </div>
@endif

{{-- PAGE BREAK: faqat Part 2 dan keyin Part 3 bo'lsa --}}
@if($hasPart2 && $hasPart3)
    <div class="page-break"></div>
@endif

{{-- ===================== PART 3 ===================== --}}
@if($hasPart3)
    <div class="section">

        <div class="section-title">PART 3</div>

        @if(!empty($part_three['resultData']))
            <div class="result-box">
                <div class="result-score">
                    {{ $part_three['resultData']['score'] ?? '-' }}
                    <span>/ {{ $part_three['resultData']['max_score'] ?? '-' }}</span>
                </div>
                <p class="result-desc">
                    {{ $part_three['resultData']['description'] ?? '' }}
                </p>
            </div>
        @endif

        @if(!empty($part_three['theme']))
            <div class="sub-section">
                <div class="sub-title">Theme</div>
                <div class="text-box">{{ $part_three['theme'] }}</div>
            </div>
        @endif

        @if(isset($part_three['forAgainstData']) && (!empty($part_three['forAgainstData']['forDetails'] ?? '') || !empty($part_three['forAgainstData']['againtsDetails'] ?? '')))
            <div class="sub-section">
                <div class="sub-title">FOR / AGAINST</div>

                <div class="text-box">
                    @if(!empty($part_three['forAgainstData']['forDetails'] ?? ''))
                        <div style="margin-bottom: 12px;">
                            <strong>FOR:</strong>
                            <div>{!! $part_three['forAgainstData']['forDetails'] !!}</div>
                        </div>
                    @endif

                    @if(!empty($part_three['forAgainstData']['againtsDetails'] ?? ''))
                        <div>
                            <strong>AGAINST:</strong>
                            <div>{!! $part_three['forAgainstData']['againtsDetails'] !!}</div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        @if(!empty($part_three['answer_text']))
            <div class="sub-section">
                <div class="sub-title">Answer</div>
                <div class="text-box">{!! $part_three['answer_text'] ?? '—' !!}</div>
            </div>
        @endif

    </div>
@endif

</body>
</html>
