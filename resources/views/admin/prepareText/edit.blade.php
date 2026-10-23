@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.prepareText.title_singular') }} | {{$types[$prepareText->type]}}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route( "admin.prepareText.update" , $prepareText->id ) }}" enctype="multipart/form-data" >
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.prepareText.fields.text') }}</label>

                <textarea class="form-control {{ $errors->has('text') ? 'is-invalid' : '' }}" name="text" id="text" required>{{ old('text', $prepareText->text) }}</textarea>
                @if($errors->has('text'))
                    <div class="invalid-feedback">
                        {{ $errors->first('text') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.prepareText.fields.name_helper') }}</span>
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
