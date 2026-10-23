@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.Speaking_exercise.title') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.speakings_exercise.part_two.update",['id'=>$speaking_exercise->id]) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="text" name="exercise_id" hidden="" value="{{$id}}">
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
                    <input class="form-control {{ $errors->has('seconds') ? 'is-invalid' : '' }}" type="number" name="seconds" id="seconds" placeholder="seconds" value="{{ old('seconds', $speaking_exercise->part_two->time) }}" required>
                    @if($errors->has('seconds'))
                        <div class="invalid-feedback">
                            {{ $errors->first('seconds') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening.fields.name_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="" for="name">{{ trans('cruds.Speaking_exercise.fields.modal_answer') }}</label>
                    <textarea class="form-control text-align-left {{ $errors->has('modal_answer') ? 'is-invalid' : '' }}"
                              name="modal_answer"
                              id="name"
                              style="height: 100px; min-height: 220px; overflow-y: auto;"
                    >{{ old('modal_answer',$speaking_exercise->part_two->modal_answer) }}</textarea>

                </div>

                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.speakings_exercise.part_two.index',['id'=>$speaking_exercise->exercise_id]) }}">
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
