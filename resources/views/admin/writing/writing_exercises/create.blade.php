@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.reading_exercise.title') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.writings.exercise.store") }}" enctype="multipart/form-data">
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
                    <label>{{ trans('cruds.writing_exercise.fields.part_category') }}</label>
                    <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="part_category" id="part_category" onchange="change_category(this)">
                        <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                        @foreach(\App\Models\WritingExercise::PART_CATEGORIES as $key => $label)
                            <option value="{{ $key }}" >{{ $label }}</option>
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
                              style="height: 50px; min-height: 70px; overflow-y: auto;"
                              required>{{ old('theme') }}</textarea>
                    @if($errors->has('theme'))
                        <div class="invalid-feedback">
                            {{ $errors->first('theme') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.writing.fields.advice_helper') }}</span>
                </div>

                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.writing.fields.requirement') }}</label>
                    <textarea class="form-control text-align-left {{ $errors->has('requirement') ? 'is-invalid' : '' }}"
                              name="requirement"
                              id="name"
                              style="height: 50px; min-height: 70px; overflow-y: auto;"
                              required>{{ old('requirement') }}</textarea>
                    @if($errors->has('requirement'))
                        <div class="invalid-feedback">
                            {{ $errors->first('requirement') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.writing.fields.advice_helper') }}</span>
                </div>

                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.writing.fields.advice') }}</label>
                    <textarea class="form-control {{ $errors->has('advice') ? 'is-invalid' : '' }}" type="text" rows="2" name="advice" id="advice" style="height: 50px; min-height: 70px; overflow-y: auto;"  required >{{ old('advice', '') }}</textarea>
                    @if($errors->has('advice'))
                        <div class="invalid-feedback">
                            {{ $errors->first('advice') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.writing.fields.advice_helper') }}</span>
                </div>

                <div class="form-group">
                    <label class="required" for="word_count">{{ trans('cruds.writing_exercise.fields.word_counts') }}</label>
                    <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" type="number" name="word_count" id="word_count" value="{{ old('word_count', '') }}" placeholder="Word count" required >
                    @if($errors->has('word_count'))
                        <div class="invalid-feedback">
                            {{ $errors->first('word_count') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening.fields.name_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="time_limit">{{ trans('cruds.writing_exercise.fields.time_limit') }}</label>
                    <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" type="number" name="time_limit" id="time_limit" value="{{ old('time_limit', '') }}" placeholder="type minutes" required >
                    @if($errors->has('time_limit'))
                        <div class="invalid-feedback">
                            {{ $errors->first('time_limit') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening.fields.name_helper') }}</span>
                </div>
                <div class="form-group {{ old('part_category') == 1 ? '' : 'd-none' }} " id="bullet_point">
                    <label class="required" for="time_limit">{{ trans('cruds.writing_exercise.fields.bullet_points') }}</label>


                    <input class="form-control mb-1 {{ $errors->has('bullet_point_1') ? 'is-invalid' : '' }}" type="text" name="bullet_point_1" id="bullet_point_1" value="{{ old('bullet_point_1', '') }}" placeholder="."  >
                    <input class="form-control mb-1 {{ $errors->has('bullet_point_2') ? 'is-invalid' : '' }}" type="text" name="bullet_point_2" id="bullet_point_2" value="{{ old('bullet_point_2', '') }}" placeholder="."  >
                    <input class="form-control {{ $errors->has('bullet_point_3') ? 'is-invalid' : '' }}" type="text" name="bullet_point_3" id="bullet_point_3" value="{{ old('bullet_point_3', '') }}" placeholder="."  >
                    @if($errors->has('bullet_point_1'))
                        <div class="invalid-feedback">
                            {{ $errors->first('bullet_point_1') }}
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
                    <a class="btn btn-default" href="{{ route('admin.writings.exercise.index',['id'=>$id]) }}">
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
    <script>


        let advise_array=[
            `Write about 150 words in an appropriate format and style.`,
            `Write at least 250 words in an appropriate style and format.`,

        ]

        function change_category(selection){
            let advise_input=document.getElementById('advice')
            let word_count=document.getElementById('word_count')
            let bullet=document.getElementById('bullet_point')
            let key=selection.value
            advise_input.value=advise_array[key-1]
            if (key==1){
                word_count.value=150;
                bullet.classList.remove('d-none')
            }else {
                word_count.value=250;
                bullet.classList.add('d-none')

            }
        }
    </script>
@endsection
