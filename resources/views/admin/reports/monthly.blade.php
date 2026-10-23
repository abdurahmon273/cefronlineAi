@extends('layouts.admin')

@section('styles')
    <style>
        .report-stat-card {
            border: 1px solid #e3e6f0;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.04);
        }

        .report-stat-card .stat-label {
            font-size: 0.95rem;
            font-weight: 600;
            color: #4b5563;
            margin-bottom: 0.5rem;
        }

        .report-stat-card .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #23272b;
            line-height: 1.1;
            margin-bottom: 1rem;
        }

        .report-channel-row {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }


        .report-progress {
            height: 10px;
            border-radius: 999px;
            background-color: #edf2f7;
            overflow: hidden;
        }

        .report-progress-bar {
            height: 100%;
            border-radius: 999px;
        }

        .report-muted-box {
            border: 1px dashed #ced4da;
            border-radius: 0.5rem;
            background: #f8f9fa;
        }

        .report-hero-title {
            font-size: 1.85rem;
            font-weight: 700;
            line-height: 1.2;
            color: #212529;
            margin-bottom: 0;
        }

        .report-toolbar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .report-toolbar-inline {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .report-toolbar .form-control {
            max-width: 210px;
        }

        .report-exercise-card {
            border: 1px solid #e3e6f0;
            border-radius: 0.5rem;
            background: #fff;
            height: 100%;
        }

        .report-exercise-card .card-body {
            padding: 1.25rem;
        }

        .report-exercise-label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
            font-size: 1rem;
            font-weight: 700;
            color: #2d3748;
        }

        .report-exercise-count {
            font-size: 2.2rem;
            font-weight: 700;
            line-height: 1;
            color: #111827;
            margin-bottom: 0.35rem;
        }

        .report-exercise-text {
            font-size: 0.9rem;
            color: #6b7280;
            margin-bottom: 1rem;
        }

        .report-exercise-split {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .report-exercise-split-item {
            flex: 1 1 0;
            min-width: 120px;
            border-radius: 0.5rem;
            background: #f8f9fa;
            border: 1px solid #e5e7eb;
            padding: 0.75rem 0.85rem;
        }

        .report-exercise-split-label {
            font-size: 0.76rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #6b7280;
            margin-bottom: 0.25rem;
        }

        .report-exercise-split-value {
            font-size: 1.15rem;
            font-weight: 700;
            color: #1f2937;
            line-height: 1.2;
        }

        .report-exercise-summary {
            border: 1px dashed #d1d5db;
            border-radius: 0.5rem;
            background: #f8f9fa;
            padding: 1rem;
        }

        .report-exercise-summary + .report-exercise-summary {
            margin-top: 0.75rem;
        }

        .report-table td,
        .report-table th {
            vertical-align: middle;
        }

        @media (max-width: 767.98px) {
            .report-hero-title {
                font-size: 1.4rem;
            }

            .report-toolbar {
                justify-content: flex-start;
            }

            .report-toolbar-inline {
                justify-content: flex-start;
            }

            .report-toolbar .btn,
            .report-toolbar .form-control {
                width: 100%;
                max-width: none;
            }
        }
    </style>
@endsection

@section('content')

    @livewire('admin.reports.daily.main-livewire',['type'=>1])



@endsection
