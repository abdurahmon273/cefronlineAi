@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.savedLesson.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.saved-lessons.update", [$savedLesson->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="user_id">{{ trans('cruds.savedLesson.fields.user') }}</label>
                <select class="form-control select2 {{ $errors->has('user') ? 'is-invalid' : '' }}" name="user_id" id="user_id" required>
                    @foreach($users as $id => $entry)
                        <option value="{{ $id }}" {{ (old('user_id') ? old('user_id') : $savedLesson->user->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('user'))
                    <div class="invalid-feedback">
                        {{ $errors->first('user') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.savedLesson.fields.user_helper') }}</span>
            </div>
            <div class="form-group">
                <label>{{ trans('cruds.savedLesson.fields.saved') }}</label>
                <select class="form-control {{ $errors->has('saved') ? 'is-invalid' : '' }}" name="saved" id="saved">
                    <option value disabled {{ old('saved', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\SavedLesson::SAVED_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('saved', $savedLesson->saved) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('saved'))
                    <div class="invalid-feedback">
                        {{ $errors->first('saved') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.savedLesson.fields.saved_helper') }}</span>
            </div>
            <div class="form-group">
                <label>{{ trans('cruds.savedLesson.fields.liked') }}</label>
                <select class="form-control {{ $errors->has('liked') ? 'is-invalid' : '' }}" name="liked" id="liked">
                    <option value disabled {{ old('liked', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\SavedLesson::LIKED_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('liked', $savedLesson->liked) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('liked'))
                    <div class="invalid-feedback">
                        {{ $errors->first('liked') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.savedLesson.fields.liked_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="video_id">{{ trans('cruds.savedLesson.fields.video') }}</label>
                <select class="form-control select2 {{ $errors->has('video') ? 'is-invalid' : '' }}" name="video_id" id="video_id" required>
                    @foreach($videos as $id => $entry)
                        <option value="{{ $id }}" {{ (old('video_id') ? old('video_id') : $savedLesson->video->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('video'))
                    <div class="invalid-feedback">
                        {{ $errors->first('video') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.savedLesson.fields.video_helper') }}</span>
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