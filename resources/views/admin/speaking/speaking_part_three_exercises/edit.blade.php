@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.Speaking_exercise.title') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.speakings_exercise.part_three.update",['id'=>$speaking_exercise->id]) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="text" name="exercise_id" hidden="" value="{{$speaking_exercise->exercise_id}}">
                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.writing.fields.title') }}</label>
                    <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" type="text" name="title" id="title" value="{{ old('title', $speaking_exercise->title) }}" required>
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
                            <option value="{{ $key }}" {{ old('free', $speaking_exercise->status) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
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
                    <label class="required" for="name">{{ trans('cruds.speaking.fields.seconds') }}</label>
                    <input class="form-control {{ $errors->has('seconds') ? 'is-invalid' : '' }}" type="number" name="seconds" id="seconds" placeholder="seconds" value="{{ old('seconds', $speaking_exercise->part_three->time) }}" required>
                    @if($errors->has('seconds'))
                        <div class="invalid-feedback">
                            {{ $errors->first('seconds') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening.fields.name_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="require" for="name">{{ trans('cruds.speaking.fields.theme') }}</label>
                    <textarea class="form-control"  name="theme"  style="font-size: 18px;height: auto; min-height: 50px" rows="3"> {{old('theme',$speaking_exercise->part_three->theme)}} </textarea>

{{--                    @if($speaking_exercise->part_three->theme_audio_url)--}}
{{--                        <audio controls style="width: 200px;margin-top: 10px">--}}
{{--                            <source src="{{ route('admin.speaking.audio.stream', $speaking_exercise->part_three->theme_audio_url) }}" type="audio/mpeg">--}}
{{--                        </audio>--}}
{{--                    @else--}}
{{--                        <span class="text-danger">No audio</span>--}}
{{--                    @endif--}}
                </div>
                <div class="form-group">
                    <label class="" for="name">{{ trans('cruds.Speaking_exercise.fields.modal_answer') }}</label>
                    <textarea class="form-control text-align-left {{ $errors->has('modal_answer') ? 'is-invalid' : '' }}"
                              name="modal_answer"
                              id="name"
                              style="height: 100px; min-height: 220px; overflow-y: auto;"
                    >{{ old('modal_answer',$speaking_exercise->part_three->modal_answer) }}</textarea>

                </div>

                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.speakings_exercise.part_three.index',['id'=>$speaking_exercise->exercise_id]) }}">
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
