@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.writing.title_full') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.writings_exercise.full.update" ,['id'=>$writing_exercise->id])  }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="text" name="exercise_id" hidden="" value="{{$id}}">
                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.writing.fields.title') }}</label>
                    <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', $writing_exercise->name) }}" required>
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
                        <option value disabled {{ old('status', $writing_exercise->status) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
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

                <div class="form-group mt-3 pt-3">
                    <h3 class="">Task One</h3>
                </div>
                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.writing.fields.theme') }}</label>
                    <textarea class="form-control text-align-left {{ $errors->has('task_one_theme') ? 'is-invalid' : '' }}"
                        name="task_one_theme"
                        id="task_one_theme"
                        style="min-height: 220px; overflow-y: auto;"
                        required> {{ old('task_one_theme', $task_one->theme) }} </textarea>
                    @if($errors->has('task_one_theme'))
                        <div class="invalid-feedback">
                            {{ $errors->first('task_one_theme') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.writing.fields.advice_helper') }}</span>
                </div>



                <div class="form-group">
                    <label class="" for="word_count">{{ trans('cruds.writing_exercise.fields.word_counts') }}</label>
                    <input class="form-control {{ $errors->has('task_one_word_count') ? 'is-invalid' : '' }}" type="number" name="task_one_word_count" id="task_one_word_count" value="{{ old('task_one_word_count', $task_one->word_count??200) }}" placeholder="Word count"  >
                    @if($errors->has('task_one_word_count'))
                        <div class="invalid-feedback">
                            {{ $errors->first('task_one_word_count') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening.fields.name_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="" for="time_limit">{{ trans('cruds.writing_exercise.fields.time_limit') }}</label>
                    <input class="form-control {{ $errors->has('task_one_time_limit') ? 'is-invalid' : '' }}" type="number" name="task_one_time_limit" id="task_one_time_limit" value="{{ old('task_one_time_limit', $task_one->time_limit??30) }}" placeholder="type minutes"  >
                    @if($errors->has('task_one_time_limit'))
                        <div class="invalid-feedback">
                            {{ $errors->first('task_one_time_limit') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening.fields.name_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="" for="name">{{ trans('cruds.writing.fields.example_text') }}</label>
                    <textarea class="form-control text-align-left {{ $errors->has('task_one_example_text') ? 'is-invalid' : '' }}"
                              name="task_one_example_text"
                              id="name"
                              style="height: 100px; min-height: 220px; overflow-y: auto;"
                    >{{ old('task_one_example_text',$task_one->example_text) }}</textarea>
                    @if($errors->has('task_one_example_text'))
                        <div class="invalid-feedback">
                            {{ $errors->first('task_one_example_text') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.writing.fields.advice_helper') }}</span>
                </div>

                <div class="form-group mt-3 pt-3">
                    <h3 class="">Task Two</h3>
                </div>

                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.writing.fields.theme') }}</label>
                    <textarea class="form-control text-align-left {{ $errors->has('task_two_theme') ? 'is-invalid' : '' }}"
                              name="task_two_theme"
                              id="name"
                              style="height: 220px; min-height: 70px; overflow-y: auto;"
                              required>{{ old('task_two_theme',$task_two->theme) }}</textarea>
                    @if($errors->has('task_two_theme'))
                        <div class="invalid-feedback">
                            {{ $errors->first('task_two_theme') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.writing.fields.advice_helper') }}</span>
                </div>



                <div class="form-group">
                    <label class="" for="word_count">{{ trans('cruds.writing_exercise.fields.word_counts') }}</label>
                    <input class="form-control {{ $errors->has('task_two_word_count') ? 'is-invalid' : '' }}" type="number" name="task_two_word_count" id="task_two_word_count" value="{{ old('task_two_word_count', $task_two->word_count??200) }}" placeholder="Word count"  >
                    @if($errors->has('task_two_word_count'))
                        <div class="invalid-feedback">
                            {{ $errors->first('task_two_word_count') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening.fields.name_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="" for="time_limit">{{ trans('cruds.writing_exercise.fields.time_limit') }}</label>
                    <input class="form-control {{ $errors->has('task_two_time_limit') ? 'is-invalid' : '' }}" type="number" name="task_two_time_limit" id="task_two_time_limit" value="{{ old('task_two_time_limit', $task_two->time_limit??30) }}" placeholder="type minutes"  >
                    @if($errors->has('task_two_time_limit'))
                        <div class="invalid-feedback">
                            {{ $errors->first('task_two_time_limit') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening.fields.name_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="" for="name">{{ trans('cruds.writing.fields.example_text') }}</label>
                    <textarea class="form-control text-align-left {{ $errors->has('task_two_example_text') ? 'is-invalid' : '' }}"
                              name="task_two_example_text"
                              id="name"
                              style="height: 100px; min-height: 220px; overflow-y: auto;"
                    >{{ old('task_two_example_text',$task_two->example_text) }}</textarea>
                    @if($errors->has('task_two_example_text'))
                        <div class="invalid-feedback">
                            {{ $errors->first('task_two_example_text') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.writing.fields.advice_helper') }}</span>
                </div>

                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.writings_exercise.full.index',['id'=>$writing_exercise->exercise_id]) }}">
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
