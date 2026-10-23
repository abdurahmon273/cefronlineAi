@extends('layouts.admin')
@section('content')
    <livewire:admin.referral.referral-index-livewire :id="$id" :start_date="$startDate" :end_date="$endDate"/>
@endsection
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        function initFlatpickr() {
            flatpickr("#start_date", {
                enableTime: true,
                dateFormat: "Y-m-d",
                onChange: function(selectedDates, dateStr) {
                    Livewire.dispatch('updated_start_date',[dateStr])
                    {{--                    @this.set('start_date', dateStr);--}}
                }
            });

            flatpickr("#end_date", {
                enableTime: true,
                dateFormat: "Y-m-d",
                onChange: function(selectedDates, dateStr) {
                    Livewire.dispatch('updated_end_date',[dateStr])
                    {{--                    @this.set('end_date', dateStr);--}}
                }
            });
        }

        document.addEventListener("DOMContentLoaded", function () {
            initFlatpickr();
        });

        Livewire.hook('message.processed', (message, component) => {
            initFlatpickr();
        });

        function change_filter_div(){
            let filter_div=document.getElementById('filter_div')

            if (filter_div.classList.contains('d-none')) {
                filter_div.classList.remove('d-none');
            }else {
                filter_div.classList.add('d-none');
            }
        }
    </script>


@endsection

