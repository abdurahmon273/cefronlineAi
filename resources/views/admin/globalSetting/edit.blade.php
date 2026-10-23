@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.globalSetting.title') }} | Gpt
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route( "admin.gptSettings.update" ) }}" enctype="multipart/form-data" >
            @method('PUT')
            @csrf

            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.globalSetting.fields.openai_chat_url') }}</label>

                <input  class="form-control {{ $errors->has('openai_chat_url') ? 'is-invalid' : '' }}" name="openai_chat_url" id="openai_chat_url" value="{{ old('openai_chat_url', $globalSetting->openai_chat_url) }}" required>

                @if($errors->has('openai_chat_url'))
                    <div class="invalid-feedback">
                        {{ $errors->first('openai_chat_url') }}
                    </div>
                @endif
            </div>

            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.globalSetting.fields.openai_model') }}</label>

                <input  class="form-control {{ $errors->has('openai_model') ? 'is-invalid' : '' }}" name="openai_model" id="openai_model" value="{{ old('openai_model', $globalSetting->openai_model) }}" required>

                @if($errors->has('openai_model'))
                    <div class="invalid-feedback">
                        {{ $errors->first('openai_model') }}
                    </div>
                @endif
            </div>

            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.globalSetting.fields.openai_max_tokens') }}</label>

                <input type="number" class="form-control {{ $errors->has('openai_max_tokens') ? 'is-invalid' : '' }}" name="openai_max_tokens" id="openai_max_tokens" value="{{ old('openai_max_tokens', $globalSetting->openai_max_tokens) }}" required>

                @if($errors->has('openai_max_tokens'))
                    <div class="invalid-feedback">
                        {{ $errors->first('openai_max_tokens') }}
                    </div>
                @endif
            </div>

            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.globalSetting.fields.openai_key') }}</label>

                <textarea rows="2" class="form-control {{ $errors->has('openai_key') ? 'is-invalid' : '' }}" name="openai_key" id="openai_key" required >{{ old('openai_key', $globalSetting->openai_key) }}</textarea>
                @if($errors->has('openai_key'))
                    <div class="invalid-feedback">
                        {{ $errors->first('openai_key') }}
                    </div>
                @endif
            </div>
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.globalSetting.fields.cloud_vision_key') }}</label>

                <textarea rows="2" class="form-control {{ $errors->has('cloud_vision_key') ? 'is-invalid' : '' }}" name="cloud_vision_key" id="cloud_vision_key" required >{{ old('cloud_vision_key', $globalSetting->cloud_vision_key) }}</textarea>
                @if($errors->has('cloud_vision_key'))
                    <div class="invalid-feedback">
                        {{ $errors->first('cloud_vision_key') }}
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
