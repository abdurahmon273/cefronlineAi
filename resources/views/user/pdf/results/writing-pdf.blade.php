<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Writing Test Result PDF</title>
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
        }
        .summary-cell:last-child {
            border-right: none;
        }
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
        .section {
            margin-bottom: 30px;
        }
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
        .sub-section {
            margin-bottom: 20px;
        }
        .sub-title {
            font-size: 12px;
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
        }
        .text-box {
            font-size: 13px;
            color: #444;
            white-space: pre-wrap;
            border-left: 1px solid #eee;
            padding-left: 15px;
        }
        .feedback-item {
            margin-bottom: 10px;
            padding: 10px;
            background: #fdfdfd;
            border: 1px solid #f0f0f0;
            border-radius: 4px;
        }
        .feedback-label {
            font-size: 11px;
            font-weight: bold;
            color: #666;
            display: block;
            margin-bottom: 2px;
        }
        .feedback-text {
            font-size: 12px;
            color: #444;
        }
        /* ===== Result score/description (webdagidek) ===== */
        .result-wrap{
            margin: 10px 0 18px 0;
            padding: 12px 14px;
            border: 1px solid #eee;
            border-radius: 6px;
            background: #fff;
        }
        .result-row{
            display: table;
            width: 100%;
        }
        .result-col{
            display: table-cell;
            vertical-align: middle;
        }
        .result-col.divider{
            width: 1%;
            padding: 0 12px;
        }
        .result-divider{
            width: 1px;
            height: 28px;
            background: #e5e7eb;
        }
        .result-score{
            font-size: 18px;
            font-weight: bold;
            color: #111;
        }
        .result-score span{
            color: #999;
            font-weight: 700;
        }
        .result-tag{
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            font-size: 11px;
            font-weight: 700;
            color: #555;
            margin-left: 10px;
        }
        .result-desc-block{
            margin-top: 12px;
        }
        .result-desc-title{
            font-size: 11px;
            font-weight: 700;
            color: #111;
            margin-bottom: 4px;
        }
        .result-desc{
            font-size: 12px;
            color: #555;
            line-height: 1.6;
            margin: 0;
            white-space: normal;
        }
        /* --- Utility classes (Tailwind-like) for PDF --- */
        .mb-4 { margin-bottom: 16px; }
        .leading-7 { line-height: 1.75; }

        .text-green-600 { color: #16a34a; }
        .text-red-500 { color: #ef4444; }

        .underline { text-decoration: underline; }
        .line-through { text-decoration: line-through; }

        .font-medium { font-weight: 500; }
        .mx-1 { margin-left: 4px; margin-right: 4px; }

        .text-box p { margin: 0 0 12px 0; }
    </style>
</head>
<body>

@php
    // ===== CONTENT FLAGS (bosh section chiqmasligi uchun) =====
    $taskOneHasPrompt = !empty($task_one['question'] ?? '');
    $taskOneHasText1  = !empty($task_one['text_1'] ?? '');
    $taskOneHasText2  = !empty($task_one['text_2'] ?? '');
    $taskOneHasEval   = (bool)($status==3 ?? false) && !empty($task_one['task_response'] ?? []);

    $hasTaskOne = isset($task_one) && ($taskOneHasPrompt || $taskOneHasText1 || $taskOneHasText2 || $taskOneHasEval);

    $taskTwoHasPrompt = !empty($task_two['question'] ?? '');
    $taskTwoHasText   = !empty($task_two['text'] ?? '');
    $taskTwoHasEval   = (bool)($status==3 ?? false) && !empty($task_two['task_response'] ?? []);
    $taskTwoHasExtra  = !empty($task_two['description'] ?? '');

    $hasTaskTwo = isset($task_two) && ($taskTwoHasPrompt || $taskTwoHasText || $taskTwoHasEval || $taskTwoHasExtra);
    $taskOneHasResult = !empty($task_one['resultData'] ?? []) || !empty($task_one['description_1'] ?? '') || !empty($task_one['description_2'] ?? '');
    $hasTaskOne = isset($task_one) && ($taskOneHasPrompt || $taskOneHasText1 || $taskOneHasText2 || $taskOneHasEval || $taskOneHasResult);

    $taskTwoHasResult = !empty($task_two['resultData'] ?? []) || !empty($task_two['description'] ?? '');
    $hasTaskTwo = isset($task_two) && ($taskTwoHasPrompt || $taskTwoHasText || $taskTwoHasEval || $taskTwoHasExtra || $taskTwoHasResult);
@endphp

<div class="header">
    <h1>Writing Test Result</h1>
    <div class="info">
        ID: #000{{ $writingResult['id'] ?? '' }} |
        Date: {{ \Carbon\Carbon::now()->format('d.m.Y H:i') }} |
        Name: {{ $writingResult['exercise']['name'] ?? 'Practice' }}
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
            {{ $writingResult->percentageData['ball'] ?? '-' }}
            /
            {{ \App\Enums\CefrTypeEnum::WRITING_MAX_SCORES[$writingResult->category_type] ?? '-' }}
        </span>
    </div>
    @if(!empty($writingResult['degree'] ?? null))
        <div class="summary-cell">
            <span class="label-small">Level</span>
            <span class="score-big">{{ $percentageData['degree'] }}</span>
        </div>
    @endif
    <div class="summary-cell">
        <span class="label-small">Status</span>
        <span style="font-size: 14px; font-weight: bold;">
            {{ ($status==3) ? 'Checked' : \App\Enums\CefrTypeEnum::WRITING_STATUS_DATA[$status] }}
        </span>
    </div>
</div>

{{-- ===================== TASK 1 ===================== --}}
@if($hasTaskOne)
    <div class="section">
        <div class="section-title">TASK 1</div>
        {{-- RESULT (Task 1.1 / Task 1.2 score + description) --}}
        @if(!empty($task_one['resultData']) || !empty($task_one['description_1']) || !empty($task_one['description_2']))
            <div class="result-wrap">
                <div class="result-row">

                    {{-- Task 1.1 --}}
                    <div class="result-col">
                <span class="result-score">
                    {{ $task_one['resultData']['score_1'] ?? '-' }}
                    <span>/ {{ $task_one['resultData']['max_score_1'] ?? '-' }}</span>
                </span>
                        <span class="result-tag">Task 1.1</span>
                    </div>

                    {{-- Divider --}}
                    <div class="result-col divider">
                        <div class="result-divider"></div>
                    </div>

                    {{-- Task 1.2 --}}
                    <div class="result-col" style="text-align:right;">
                <span class="result-score">
                    {{ $task_one['resultData']['score_2'] ?? '-' }}
                    <span>/ {{ $task_one['resultData']['max_score_2'] ?? '-' }}</span>
                </span>
                        <span class="result-tag">Task 1.2</span>
                    </div>
                </div>

                {{-- Descriptions --}}
                <div class="result-desc-block">
                    @if(!empty($task_one['description_1']))
                        <div style="margin-bottom: 10px;">
                            <div class="result-desc-title">Task 1.1 description</div>
                            <p class="result-desc">{{ $task_one['description_1'] }}</p>
                        </div>
                    @endif

                    @if(!empty($task_one['description_2']))
                        <div>
                            <div class="result-desc-title">Task 1.2 description</div>
                            <p class="result-desc">{{ $task_one['description_2'] }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        @if($taskOneHasEval)
            <div class="sub-title" style="margin-top: 20px;">AI Evaluation:</div>

            <div class="feedback-item">
                <span class="feedback-label">Task Achievement</span>
                <div class="feedback-text">{!! nl2br(e($task_one['task_response']['task_achievement'] ?? '')) !!}</div>
            </div>

            <div class="feedback-item">
                <span class="feedback-label">Coherence and Cohesion</span>
                <div class="feedback-text">{!! nl2br(e($task_one['task_response']['coherence_and_cohesion'] ?? '')) !!}</div>
            </div>

            <div class="feedback-item">
                <span class="feedback-label">Lexical Resource</span>
                <div class="feedback-text">{!! nl2br(e($task_one['task_response']['lexical_resource'] ?? '')) !!}</div>
            </div>

            <div class="feedback-item">
                <span class="feedback-label">Grammatical Range and Accuracy</span>
                <div class="feedback-text">{!! nl2br(e($task_one['task_response']['grammatical_range_and_accuracy'] ?? '')) !!}</div>
            </div>
        @endif

        @if($taskOneHasPrompt)
            <div class="sub-section">
                <div class="sub-title">Prompt:</div>
                <div class="text-box">{!! nl2br(e($task_one['question'])) !!}</div>
            </div>
        @endif

        @if($taskOneHasText1)
            <div class="sub-section">
                <div class="sub-title">Answer Part 1.1:</div>
                <div class="text-box">{!! $task_one['text_1'] !!}</div>
            </div>
        @endif

        @if($taskOneHasText2)
            <div class="sub-section">
                <div class="sub-title">Answer Part 1.2:</div>
                <div class="text-box">{!! $task_one['text_2'] !!}</div>
            </div>
        @endif

    </div>
@endif

{{-- ===================== TASK 2 ===================== --}}
@if($hasTaskTwo)
    <div class="section">
        <div class="section-title">TASK 2</div>

        {{-- RESULT (Task 2 score + description) --}}
        @if(!empty($task_two['resultData']) || !empty($task_two['description']))
            <div class="result-wrap">
                <div>
            <span class="result-score">
                {{ $task_two['resultData']['score'] ?? '-' }}
                <span>/ {{ $task_two['resultData']['max_score'] ?? '-' }}</span>
            </span>
                    <span class="result-tag">Task 2</span>
                </div>

                @if(!empty($task_two['description']))
                    <div class="result-desc-block">
                        <div class="result-desc-title">Task 2 description</div>
                        <p class="result-desc">{{ $task_two['description'] }}</p>
                    </div>
                @endif
            </div>
        @endif

        @if($taskTwoHasEval)
            <div class="sub-title" style="margin-top: 20px;">AI Evaluation:</div>

            <div class="feedback-item">
                <span class="feedback-label">Task Response</span>
                <div class="feedback-text">{!! nl2br(e($task_two['task_response']['task_achievement'] ?? '')) !!}</div>
            </div>

            <div class="feedback-item">
                <span class="feedback-label">Coherence and Cohesion</span>
                <div class="feedback-text">{!! nl2br(e($task_two['task_response']['coherence_and_cohesion'] ?? '')) !!}</div>
            </div>

            <div class="feedback-item">
                <span class="feedback-label">Lexical Resource</span>
                <div class="feedback-text">{!! nl2br(e($task_two['task_response']['lexical_resource'] ?? '')) !!}</div>
            </div>

            <div class="feedback-item">
                <span class="feedback-label">Grammatical Range and Accuracy</span>
                <div class="feedback-text">{!! nl2br(e($task_two['task_response']['grammatical_range_and_accuracy'] ?? '')) !!}</div>
            </div>
        @endif

        @if($taskTwoHasPrompt)
            <div class="sub-section">
                <div class="sub-title">Prompt:</div>
                <div class="text-box">{!! nl2br(e($task_two['question'])) !!}</div>
            </div>
        @endif

        @if($taskTwoHasText)
            <div class="sub-section">
                <div class="sub-title">Answer:</div>
                <div class="text-box">{!! $task_two['text'] !!}</div>
            </div>
        @endif


    </div>
@endif

</body>
</html>
