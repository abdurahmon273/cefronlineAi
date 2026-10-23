@extends('layouts.admin')

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Umumiy hisobotlar</span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8 mb-3 mb-md-0">
                    <h5 class="mb-2">Umumiy hisobot sahifasi</h5>
                    <p class="text-muted mb-0">
                    </p>
                </div>
                <div class="col-md-4 text-md-right">
                    <button class="btn btn-outline-secondary btn-sm" type="button">Hammasi</button>
                </div>
            </div>

            <hr>

            <div class="alert alert-light border mb-0">
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span>Get Users Meta Data</span>
        </div>
        <div class="card-body">
            @livewire('admin.reports.get-users-meta-data-livewire')
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span>Get Writing Results</span>
        </div>
        <div class="card-body">
            @livewire('admin.reports.get-writing-results-livewire')
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span>Get Speaking Results</span>
        </div>
        <div class="card-body">
            @livewire('admin.reports.get-speaking-results-livewire')
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        function initOverviewFlatpickr() {
            flatpickr("#start_date", {
                enableTime: true,
                dateFormat: "Y-m-d",
                onChange: function (selectedDates, dateStr) {
                    Livewire.dispatch('updated_start_date', [dateStr]);
                }
            });

            flatpickr("#end_date", {
                enableTime: true,
                dateFormat: "Y-m-d",
                onChange: function (selectedDates, dateStr) {
                    Livewire.dispatch('updated_end_date', [dateStr]);
                }
            });

            flatpickr("#writing_start_date", {
                enableTime: true,
                dateFormat: "Y-m-d",
                onChange: function (selectedDates, dateStr) {
                    Livewire.dispatch('updated_writing_start_date', [dateStr]);
                }
            });

            flatpickr("#writing_end_date", {
                enableTime: true,
                dateFormat: "Y-m-d",
                onChange: function (selectedDates, dateStr) {
                    Livewire.dispatch('updated_writing_end_date', [dateStr]);
                }
            });

            flatpickr("#speaking_start_date", {
                enableTime: true,
                dateFormat: "Y-m-d",
                onChange: function (selectedDates, dateStr) {
                    Livewire.dispatch('updated_speaking_start_date', [dateStr]);
                }
            });

            flatpickr("#speaking_end_date", {
                enableTime: true,
                dateFormat: "Y-m-d",
                onChange: function (selectedDates, dateStr) {
                    Livewire.dispatch('updated_speaking_end_date', [dateStr]);
                }
            });
        }

        document.addEventListener("DOMContentLoaded", function () {
            initOverviewFlatpickr();
        });

        Livewire.hook('message.processed', (message, component) => {
            initOverviewFlatpickr();
        });
    </script>
@endsection
