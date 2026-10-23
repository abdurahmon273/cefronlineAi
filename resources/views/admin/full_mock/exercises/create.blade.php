@extends('layouts.admin')

@section('styles')
    <style>
        /*.products_button:focus,*/

        /*.active_button {*/
        /*    !*background-color: #1a9a1a !important; !* Faol holat *!*!*/
        /*    color: black; !* Ixtiyoriy: matn rangini o'zgartirish *!*/
        /*    outline: none; !* Outline-ni olib tashlash *!*/
        /*}*/

        .products_button{
            background-color: #e3dfdf !important; /* Faol holat */
            border-bottom: 2px solid;
            color: black;
        }

        .products_button:hover{
            background-color: #c2bfbf !important; /* Faol holat */
            border-bottom: 1px solid black ;
            color: black;
        }

        /* Skrollni to'xtatish uchun */
        .list-group {
            overflow: hidden; /* Skrollni olib tashlaydi */
        }
    </style>
@endsection
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.exercise.full') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.full.exercises.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.listening.fields.title') }}</label>
                <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" type="text" name="title" id="title" value="{{ old('title', '') }}" required>
                @if($errors->has('title'))
                    <div class="invalid-feedback">
                        {{ $errors->first('title') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.listening.fields.name_helper') }}</span>
            </div>
{{--            <div class="form-group">--}}
{{--                <label class="required" for="roles">{{ trans('cruds.listening.title') }}</label>--}}

{{--                <select class="form-control select2 {{ $errors->has('listening_id') ? 'is-invalid' : '' }}" name="listening_id" id="listening_id"  required>--}}
{{--                    @foreach($listenings as $id => $listening)--}}
{{--                        <option value="{{ $listening['id'] }}" {{ (in_array($listening['id'], old('listening_id', []))) ? 'selected' : '' }}>{{ $listening['title'] }} <p>14.02.2007</p> </option>--}}
{{--                    @endforeach--}}
{{--                </select>--}}
{{--                @if($errors->has('listening_id'))--}}
{{--                    <div class="invalid-feedback">--}}
{{--                        {{ $errors->first('listening_id') }}--}}
{{--                    </div>--}}
{{--                @endif--}}
{{--                <span class="help-block">{{ trans('cruds.user.fields.roles_helper') }}</span>--}}
{{--            </div>--}}
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.listening.exercises.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection

@section('scripts')
    <!-- JavaScript -->
    <script>
        document.addEventListener('searched', function () {
            let customer_input = document.getElementById('customer-input');
            let inputWidth = 200;
            if (customer_input) {
                inputWidth = customer_input.offsetWidth;
            }
            if (inputWidth) {
                Livewire.dispatch('input_width', [inputWidth]);
            }
        });

        document.addEventListener('click', function (event) {
            let isClickInsideDiv = document.getElementById('search-box-customer');
            if (isClickInsideDiv) {
                isClickInsideDiv = isClickInsideDiv.contains(event.target);
            }

            if (!isClickInsideDiv) {
                Livewire.dispatch('outside_click');
            }
        });

        document.addEventListener('not_selected_customer', function () {
            setTimeout(function () {
                let isClickInsideDiv = document.getElementById('customer-input');
                if (isClickInsideDiv) {
                    isClickInsideDiv.click();
                    isClickInsideDiv.focus();
                }
            });
        });
    </script>

@endsection
