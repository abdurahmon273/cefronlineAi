@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.writingPrompt.title_singular') }} | {{$types[$speakingPrompt->type]}}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route( "admin.speakingPrompt.update" , $speakingPrompt->id ) }}" enctype="multipart/form-data" >
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.writingPrompt.fields.text_1') }}</label>

                <textarea rows="20" class="form-control {{ $errors->has('input_prompt_text') ? 'is-invalid' : '' }}" name="input_prompt_text" id="input_prompt_text" required>{{ old('input_prompt_text', $speakingPrompt->input_prompt_text) }}</textarea>
                @if($errors->has('input_prompt_text'))
                    <div class="invalid-feedback">
                        {{ $errors->first('input_prompt_text') }}
                    </div>
                @endif
            </div>
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.writingPrompt.fields.text_2') }}</label>

                <textarea rows="20" class="form-control {{ $errors->has('output_prompt_text') ? 'is-invalid' : '' }}" name="output_prompt_text" id="output_prompt_text" required>{{ old('output_prompt_text', $speakingPrompt->output_prompt_text) }}</textarea>
                @if($errors->has('output_prompt_text'))
                    <div class="invalid-feedback">
                        {{ $errors->first('output_prompt_text') }}
                    </div>
                @endif
            </div>

            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection
