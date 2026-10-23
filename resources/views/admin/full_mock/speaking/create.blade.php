@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.Speaking_exercise.title') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.full.speaking_exercise.store") }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="text" name="exercise_id" hidden="" value="{{$id}}">
                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.writing.fields.title') }}</label>
                    <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" type="text" name="title" id="title" value="{{ old('title',$FullExercise->speaking_exercise->title) }}" required>
                    @if($errors->has('title'))
                        <div class="invalid-feedback">
                            {{ $errors->first('title') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.writing.fields.name_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.speaking.fields.title_part_two') }} {{ trans('cruds.speaking.fields.seconds') }}</label>
                    <input class="form-control {{ $errors->has('part_two_seconds') ? 'is-invalid' : '' }}" type="number" name="part_two_seconds" id="part_two_seconds" placeholder="part_two_seconds" value="{{ old('part_two_seconds', 120) }}" required>
                    @if($errors->has('part_two_seconds'))
                        <div class="invalid-feedback">
                            {{ $errors->first('part_two_seconds') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening.fields.name_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.speaking.fields.title_part_three') }} {{ trans('cruds.speaking.fields.seconds') }}</label>
                    <input class="form-control {{ $errors->has('part_three_seconds') ? 'is-invalid' : '' }}" type="number" name="part_three_seconds" id="part_three_seconds" placeholder="part_three_seconds" value="{{ old('part_three_seconds', 120) }}" required>
                    @if($errors->has('part_three_seconds'))
                        <div class="invalid-feedback">
                            {{ $errors->first('part_three_seconds') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening.fields.name_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="require" for="name"> {{ trans('cruds.speaking.fields.title_part_three') }}  {{ trans('cruds.speaking.fields.theme') }}</label>
                    <textarea class="form-control"  name="theme"  style="font-size: 18px;height: auto; min-height: 50px" rows="3"> {{old('theme')}} </textarea>

                </div>

                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.speaking.fields.title_part_two') }} {{ trans('cruds.speaking.fields.modal_answer') }}</label>
                    <textarea class="form-control text-align-left "
                              name="part_two_modal_answer"
                              id="name"
                              style="height: 100px; min-height: 220px; overflow-y: auto;"
                    >{{ old('part_two_modal_answer') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.speaking.fields.title_part_three') }} {{ trans('cruds.speaking.fields.modal_answer') }}</label>
                    <textarea class="form-control text-align-left "
                              name="part_three_modal_answer"
                              id="name"
                              style="height: 100px; min-height: 220px; overflow-y: auto;"
                    >{{ old('part_three_modal_answer') }}</textarea>

                </div>

                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.full_exercise.more.index',['id'=>$id]) }}">
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

    </script>
@endsection
