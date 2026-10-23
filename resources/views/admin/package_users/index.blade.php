@extends('layouts.admin')

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

@endsection

@section('content')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            @can('premium_users_create')
            <a class="btn btn-success" href="{{ route('admin.package_users.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.package_users.title') }}
            </a>
            @endcan
        </div>
    </div>
<div class="card">
    <div class="card-header">
        {{ trans('cruds.package_users.title') }}
    </div>

    <div class="card-body">
         @livewire('admin.package.package-users-index-livewire')
    </div>

</div>



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
