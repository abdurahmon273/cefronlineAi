@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.exercise.full_exercise') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.full_exercise.store") }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="text" name="exercise_id" hidden="" value="{{$id}}">
                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.writing.fields.title') }}</label>
                    <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" type="text" name="title" id="title" value="{{ old('title', '') }}" required>
                    @if($errors->has('title'))
                        <div class="invalid-feedback">
                            {{ $errors->first('title') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.writing.fields.name_helper') }}</span>
                </div>
                <div class="form-group">
                    <label>{{ trans('cruds.reading_exercise.fields.status') }}</label>
                    <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status" id="status">
                        <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                        @foreach(App\Models\Course::STATUS as $key => $label)
                            <option value="{{ $key }}" {{ old('free', '0') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('status'))
                        <div class="invalid-feedback">
                            {{ $errors->first('status') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.course.fields.published_helper') }}</span>
                </div>

                <div class="form-group pt-2">
                    <label class="required" for="name">{{ trans('cruds.listening.title_exercise') }} {{ trans('cruds.writing.fields.title') }}</label>
                    <input class="form-control {{ $errors->has('listening_title') ? 'is-invalid' : '' }}" type="text" name="listening_title" id="listening_title" value="{{ old('listening_title', '') }}" required>
                    @if($errors->has('listening_title'))
                        <div class="invalid-feedback">
                            {{ $errors->first('listening_title') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.writing.fields.name_helper') }}</span>
                </div>


                <div class="form-group pt-2">
                    <label class="required" for="name">{{ trans('cruds.reading.title_exercise') }} {{ trans('cruds.writing.fields.title') }}</label>
                    <input class="form-control {{ $errors->has('reading_title') ? 'is-invalid' : '' }}" type="text" name="reading_title" id="reading_title" value="{{ old('reading_title', '') }}" required>
                    @if($errors->has('reading_title'))
                        <div class="invalid-feedback">
                            {{ $errors->first('reading_title') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.writing.fields.name_helper') }}</span>
                </div>

                <div class="form-group pt-2">
                    <label class="required" for="name">{{ trans('cruds.writing.title_exercise') }} {{ trans('cruds.writing.fields.title') }}</label>
                    <input class="form-control {{ $errors->has('writing_title') ? 'is-invalid' : '' }}" type="text" name="writing_title" id="writing_title" value="{{ old('writing_title', '') }}" required>
                    @if($errors->has('writing_title'))
                        <div class="invalid-feedback">
                            {{ $errors->first('writing_title') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.writing.fields.name_helper') }}</span>
                </div>

                <div class="form-group pt-2">
                    <label class="required" for="name">{{ trans('cruds.speaking.title_exercise') }} {{ trans('cruds.speaking.fields.title') }}</label>
                    <input class="form-control {{ $errors->has('speaking_title') ? 'is-invalid' : '' }}" type="text" name="speaking_title" id="speaking_title" value="{{ old('speaking_title', '') }}" required>
                    @if($errors->has('speaking_title'))
                        <div class="invalid-feedback">
                            {{ $errors->first('speaking_title') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.writing.fields.name_helper') }}</span>
                </div>


                <div class="form-group">
                    <label>{{ trans('cruds.listening_exercise.fields.difficulty') }}</label>
                    <select class="form-control {{ $errors->has('difficulty') ? 'is-invalid' : '' }}" name="difficulty" id="difficulty" onchange="change_category(this)">
                        <option value disabled {{ old('difficulty', null) === null ? '' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                        @foreach(\App\Models\ListeningExercise::DIFFICULTIES as $key => $label)
                            <option value="{{ $key }}" {{ old('difficulty', 1) === $key ? 'selected' : '' }} >{{ $label }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('difficulty'))
                        <div class="invalid-feedback">
                            {{ $errors->first('difficulty') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.course.fields.published_helper') }}</span>
                </div>

                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.writings_exercise.full.index',['id'=>$id]) }}">
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

@endsection
