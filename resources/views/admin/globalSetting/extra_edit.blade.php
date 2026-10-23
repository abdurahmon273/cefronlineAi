@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.globalSetting.title') }} | Gpt
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route( "admin.extraSettings.update" ) }}" enctype="multipart/form-data" >
            @method('PUT')
            @csrf

            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.globalSetting.fields.referral_profit_percentage') }}</label>

                <input type="number" class="form-control {{ $errors->has('referral_profit_percentage') ? 'is-invalid' : '' }}" name="referral_profit_percentage" id="referral_profit_percentage" value="{{ old('referral_profit_percentage', $globalSetting->referral_profit_percentage) }}" required>

                @if($errors->has('referral_profit_percentage'))
                    <div class="invalid-feedback">
                        {{ $errors->first('referral_profit_percentage') }}
                    </div>
                @endif
            </div>

            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.globalSetting.fields.admin_contact') }}</label>

                <input type="text" class="form-control {{ $errors->has('admin_contact') ? 'is-invalid' : '' }}" name="admin_contact" id="admin_contact" value="{{ old('admin_contact', $globalSetting->admin_contact) }}" required>

                @if($errors->has('admin_contact'))
                    <div class="invalid-feedback">
                        {{ $errors->first('admin_contact') }}
                    </div>
                @endif
            </div>
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.globalSetting.fields.start_referral_date') }}</label>

                <input type="text" class="form-control {{ $errors->has('start_referral_date') ? 'is-invalid' : '' }}" name="start_referral_date" id="start_referral_date" value="{{ old('start_referral_date', optional(\Carbon\Carbon::parse($globalSetting->start_referral_date))->format('Y-m-d')) }}" required>

                @if($errors->has('start_referral_date'))
                    <div class="invalid-feedback">
                        {{ $errors->first('start_referral_date') }}
                    </div>
                @endif
            </div>

            <div class="form-group text-end">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection
