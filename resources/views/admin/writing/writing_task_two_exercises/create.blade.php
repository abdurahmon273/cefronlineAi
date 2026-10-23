@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.writing.title_task_two') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.writings_exercise.task_two.store") }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="text" name="exercise_id" hidden="" value="{{$id}}">
                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.writing.fields.title') }}</label>
                    <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                    @if($errors->has('name'))
                        <div class="invalid-feedback">
                            {{ $errors->first('name') }}
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

                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.writing.fields.theme') }}</label>
                    <textarea class="form-control text-align-left {{ $errors->has('theme') ? 'is-invalid' : '' }}"
                              name="theme"
                              id="name"
                              style="height: 220px; min-height: 70px; overflow-y: auto;"
                              required>{{ old('theme') }}</textarea>
                    @if($errors->has('theme'))
                        <div class="invalid-feedback">
                            {{ $errors->first('theme') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.writing.fields.advice_helper') }}</span>
                </div>



                <div class="form-group">
                    <label class="" for="word_count">{{ trans('cruds.writing_exercise.fields.word_counts') }}</label>
                    <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" type="number" name="word_count" id="word_count" value="{{ old('word_count', 200) }}" placeholder="Word count"  >
                    @if($errors->has('word_count'))
                        <div class="invalid-feedback">
                            {{ $errors->first('word_count') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening.fields.name_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="" for="time_limit">{{ trans('cruds.writing_exercise.fields.time_limit') }}</label>
                    <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" type="number" name="time_limit" id="time_limit" value="{{ old('time_limit', 30) }}" placeholder="type minutes"  >
                    @if($errors->has('time_limit'))
                        <div class="invalid-feedback">
                            {{ $errors->first('time_limit') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening.fields.name_helper') }}</span>
                </div>

                <div class="form-group">
                    <label class="" for="name">{{ trans('cruds.writing.fields.example_text') }}</label>
                    <textarea class="form-control text-align-left {{ $errors->has('example_text') ? 'is-invalid' : '' }}"
                              name="example_text"
                              id="name"
                              style="height: 100px; min-height: 220px; overflow-y: auto;"
                              >{{ old('example_text') }}</textarea>
                    @if($errors->has('example_text'))
                        <div class="invalid-feedback">
                            {{ $errors->first('example_text') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.writing.fields.advice_helper') }}</span>
                </div>
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.writings_exercise.task_two.index',['id'=>$id]) }}">
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
