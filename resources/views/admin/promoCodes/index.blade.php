@extends('layouts.admin')

@section('styles')

@endsection

@section('content')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            @can('promoCode_create')
            <a class="btn btn-success" href="{{ route('admin.promoCodes.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.premium.fields.promo_codes') }}
            </a>
            @endcan
            @can('promoCode_create')
            <a class="btn btn-success" href="{{ route('admin.promoCodes.create.once') }}">
                {{ trans('global.add') }} {{ trans('cruds.promoCode.title') }}
            </a>
            @endcan
        </div>
    </div>
<div class="card">
    <div class="card-header">
        {{ trans('cruds.promoCode.title') }}
    </div>

    <div class="card-body">
        @livewire('premium.promo-code.index-livewire')
    </div>

</div>



@endsection
@section('scripts')
    <script>
        function initFlatpickr() {
            flatpickr("#start_date", {
                enableTime: true,
                dateFormat: "Y-m-d",
                onChange: (selectedDates, dateStr) => {
                    Livewire.dispatch('updated_start_date', [dateStr]);
                }
            });

            flatpickr("#end_date", {
                enableTime: true,
                dateFormat: "Y-m-d",
                onChange: (selectedDates, dateStr) => {
                    Livewire.dispatch('updated_end_date', [dateStr]);
                }
            });
        }

        function initPlansSelect2() {
            const $el = $('#plans');

            if ($el.data('select2')) {
                $el.off('change');
                $el.select2('destroy');
            }

            $el.select2({ width: '100%' });

            $el.on('change', function () {
                const vals = $(this).val() || [];
                Livewire.dispatch('plans_changed', [vals]);
            });
        }

        function initPackagesSelect2() {
            const $el = $('#packages');

            if ($el.data('select2')) {
                $el.off('change');
                $el.select2('destroy');
            }

            $el.select2({ width: '100%' });

            $el.on('change', function () {
                const vals = $(this).val() || [];
                Livewire.dispatch('packages_changed', [vals]);
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            initFlatpickr();
            initPlansSelect2();
            initPackagesSelect2();
        });

        Livewire.hook('message.processed', () => {
            initFlatpickr();
            initPlansSelect2();
            initPackagesSelect2();
        });
    </script>
@endsection

