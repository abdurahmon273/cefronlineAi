@extends('layouts.admin')
@section('styles')
    <style>
        .width35{
            width: 35% !important;
        }
        .DisplayNone{
            display: none !important;
        }
    </style>
@endsection
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.premium_users.title_singular') }}
        </div>

        <div class="card-body">
            <form  method="POST" action="{{ route("admin.promoCodes.store") }}"  enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="required" for="amount">{{ trans('cruds.promoCode.fields.amount') }}</label>
                    <input class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}" type="text" name="amount" id="amount" value="{{ old('amount', '') }}" required>
                    @if($errors->has('amount'))
                        <div class="invalid-feedback">
                            {{ $errors->first('amount') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.promoCode.fields.amount_helper') }}</span>
                </div>
                <div class="form-group">
                    <label>{{ trans('cruds.promoCode.fields.type') }}</label>
                    <select class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}" wire:model="type" name="type" id="type">
                        <option value  >{{ trans('global.pleaseSelect') }}</option>
                        @foreach($types as $key => $type)
                            <option  {{old('type','')==$key ? 'selected' : ''}}  value="{{ $key }}" >{{ $type }}</option>
                        @endforeach
                    </select>
                    @error('type') <span class="text-danger">{{ $message }}</span> @enderror

                    <span class="help-block">{{ trans('cruds.promoCode.fields.type_helper') }}</span>
                </div>

                <div class="form-group">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label>{{ trans('cruds.promoCode.fields.start_date') }}</label>
                            <input
                                type="text"
                                id="start_date"
                                name="start_date"
                                class="form-control {{ $errors->has('start_date') ? 'is-invalid' : '' }}"
                                value="{{ old('start_date','') }}"
                                placeholder="YYYY-MM-DD"
                            >
                            @error('start_date') <span class="text-danger">{{ $message }}</span> @enderror
                            <span class="help-block">{{ trans('cruds.promoCode.fields.start_date_helper') }}</span>
                        </div>

                        <div class="col-12 col-md-6">
                            <label>{{ trans('cruds.promoCode.fields.end_date') }}</label>
                            <input
                                type="text"
                                id="end_date"
                                name="end_date"
                                class="form-control {{ $errors->has('end_date') ? 'is-invalid' : '' }}"
                                value="{{ old('end_date','') }}"
                                placeholder="YYYY-MM-DD"
                            >
                            @error('end_date') <span class="text-danger">{{ $message }}</span> @enderror
                            <span class="help-block">{{ trans('cruds.promoCode.fields.end_date_helper') }}</span>
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <label class="required" for="discount">{{ trans('cruds.promoCode.fields.discount') }}</label>
                    <input  class="form-control {{ $errors->has('discount') ? 'is-invalid' : '' }}" type="text" name="discount" id="discountInput" value="{{ old('discount', '') }}" required>
                    @if($errors->has('discount'))
                        <div class="invalid-feedback">
                            {{ $errors->first('discount') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.promoCode.fields.discount_helper') }}</span>
                </div>

                <div class="form-group">
                    <label class="required" for="plans">{{ trans('cruds.promoCode.fields.plans') }}</label>
                    <div style="padding-bottom: 4px">
                        <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
                        <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                    </div>
                    <select class="form-control select2 {{ $errors->has('plans') ? 'is-invalid' : '' }}" name="plans[]" id="plans" multiple >
                        @foreach($plans as $id => $plan)
                            <option value="{{ $id }}" {{ in_array($id, old('plans', [])) ? 'selected' : '' }}>{{ $plan }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('plans'))
                        <div class="invalid-feedback">
                            {{ $errors->first('plans') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.promoCode.fields.plans_helper') }}</span>
                </div>

                <div class="form-group">
                    <label class="required" for="packages">{{ trans('cruds.promoCode.fields.packages') }}</label>
                    <div style="padding-bottom: 4px">
                        <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
                        <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                    </div>
                    <select class="form-control select2 {{ $errors->has('packages') ? 'is-invalid' : '' }}" name="packages[]" id="packages" multiple >
                        @foreach($packages as $id => $plan)
                            <option value="{{ $id }}" {{ in_array($id, old('packages', [])) ? 'selected' : '' }}>{{ $plan }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('packages'))
                        <div class="invalid-feedback">
                            {{ $errors->first('packages') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.promoCode.fields.plans_helper') }}</span>
                </div>

                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.promoCodes.index') }}">
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

    <script>
        function initFlatpickr() {
            flatpickr("#start_date", {
                enableTime: true,
                dateFormat: "Y-m-d",
                onChange: function(selectedDates, dateStr) {
                    Livewire.dispatch('updated_start_date',[dateStr])
                }
            });

            flatpickr("#end_date", {
                enableTime: true,
                dateFormat: "Y-m-d",
                onChange: function(selectedDates, dateStr) {
                    Livewire.dispatch('updated_end_date',[dateStr])
                }
            });
        }

        document.addEventListener("DOMContentLoaded", function () {
            initFlatpickr();

            const discountInput = document.getElementById("discountInput");

            discountInput.addEventListener("input", function () {
                let val = this.value.replace(/[^0-9]/g, ""); // faqat raqam qoldiradi
                if (val !== "") {
                    val = parseInt(val, 10);

                    if (val < 1) {
                        val = 1;
                    } else if (val > 100) {
                        val = 100;
                    }
                }
                this.value = val;
            });
        });
    </script>
@endsection

