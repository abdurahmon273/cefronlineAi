@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.application.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.applications.update", [$application->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="user_id">{{ trans('cruds.application.fields.user') }}</label>
                <select class="form-control select2 {{ $errors->has('user') ? 'is-invalid' : '' }}" name="user_id" id="user_id" required>
                    @foreach($users as $id => $entry)
                        <option value="{{ $id }}" {{ (old('user_id') ? old('user_id') : $application->user->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('user'))
                    <div class="invalid-feedback">
                        {{ $errors->first('user') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.application.fields.user_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="news_id">{{ trans('cruds.application.fields.news') }}</label>
                <select class="form-control select2 {{ $errors->has('news') ? 'is-invalid' : '' }}" name="news_id" id="news_id" required>
                    @foreach($news as $id => $entry)
                        <option value="{{ $id }}" {{ (old('news_id') ? old('news_id') : $application->news->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('news'))
                    <div class="invalid-feedback">
                        {{ $errors->first('news') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.application.fields.news_helper') }}</span>
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