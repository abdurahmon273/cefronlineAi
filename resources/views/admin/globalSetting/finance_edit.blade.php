@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.globalSetting.title') }} | Gpt
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route( "admin.financeSettings.update" ) }}" enctype="multipart/form-data" >
            @method('PUT')
            @csrf

            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.globalSetting.fields.gpt_price_per_input_token') }}</label>

                <input  class="form-control {{ $errors->has('gpt_price_per_input_token') ? 'is-invalid' : '' }}" name="gpt_price_per_input_token" id="gpt_price_per_input_token" value="{{ old('gpt_price_per_input_token', $globalSetting->gpt_price_per_input_token) }}" required>

                @if($errors->has('gpt_price_per_input_token'))
                    <div class="invalid-feedback">
                        {{ $errors->first('gpt_price_per_input_token') }}
                    </div>
                @endif
            </div>

            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.globalSetting.fields.gpt_price_per_output_token') }}</label>

                <input  class="form-control {{ $errors->has('gpt_price_per_output_token') ? 'is-invalid' : '' }}" name="gpt_price_per_output_token" id="gpt_price_per_output_token" value="{{ old('gpt_price_per_output_token', $globalSetting->gpt_price_per_output_token) }}" required>

                @if($errors->has('gpt_price_per_output_token'))
                    <div class="invalid-feedback">
                        {{ $errors->first('gpt_price_per_output_token') }}
                    </div>
                @endif
            </div>

            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.globalSetting.fields.min_ai_credit_price') }}</label>

                <input  class="form-control {{ $errors->has('min_ai_credit_price') ? 'is-invalid' : '' }}" name="min_ai_credit_price" id="min_ai_credit_price" value="{{ old('min_ai_credit_price', $globalSetting->min_ai_credit_price) }}" required>

                @if($errors->has('min_ai_credit_price'))
                    <div class="invalid-feedback">
                        {{ $errors->first('min_ai_credit_price') }}
                    </div>
                @endif
            </div>
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.globalSetting.fields.usd_current_currency') }}</label>

                <input  class="form-control {{ $errors->has('usd_current_currency') ? 'is-invalid' : '' }}" name="usd_current_currency" id="usd_current_currency" value="{{ old('usd_current_currency', $globalSetting->usd_current_currency) }}" required>

                @if($errors->has('usd_current_currency'))
                    <div class="invalid-feedback">
                        {{ $errors->first('usd_current_currency') }}
                    </div>
                @endif
            </div>

            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.globalSetting.fields.chat_gpt_tax') }}</label>

                <input type="number" class="form-control {{ $errors->has('chat_gpt_tax') ? 'is-invalid' : '' }}" name="chat_gpt_tax" id="chat_gpt_tax" value="{{ old('chat_gpt_tax', $globalSetting->chat_gpt_tax) }}" required>

                @if($errors->has('chat_gpt_tax'))
                    <div class="invalid-feedback">
                        {{ $errors->first('chat_gpt_tax') }}
                    </div>
                @endif
            </div>
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.globalSetting.fields.profit_tax') }}</label>

                <input type="number" class="form-control {{ $errors->has('profit_tax') ? 'is-invalid' : '' }}" name="profit_tax" id="profit_tax" value="{{ old('profit_tax', $globalSetting->profit_tax) }}" required>

                @if($errors->has('profit_tax'))
                    <div class="invalid-feedback">
                        {{ $errors->first('profit_tax') }}
                    </div>
                @endif
            </div>
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.globalSetting.fields.one_writing_price') }}</label>

                <input type="number" class="form-control {{ $errors->has('one_writing_price') ? 'is-invalid' : '' }}" name="one_writing_price" id="one_writing_price" value="{{ old('one_writing_price', $globalSetting->one_writing_price) }}" required>

                @if($errors->has('one_writing_price'))
                    <div class="invalid-feedback">
                        {{ $errors->first('one_writing_price') }}
                    </div>
                @endif
            </div>
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.globalSetting.fields.one_speaking_price') }}</label>

                <input type="number" class="form-control {{ $errors->has('one_speaking_price') ? 'is-invalid' : '' }}" name="one_speaking_price" id="one_speaking_price" value="{{ old('one_speaking_price', $globalSetting->one_speaking_price) }}" required>

                @if($errors->has('one_speaking_price'))
                    <div class="invalid-feedback">
                        {{ $errors->first('one_speaking_price') }}
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
