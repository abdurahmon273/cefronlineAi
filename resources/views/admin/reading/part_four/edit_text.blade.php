@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }}  {{ trans('cruds.reading_part_four.title_text') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.part4.text.update", [$part_four->id] ) }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="reading_id" value="{{$part_four->reading_exercise_id}}">
                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.listening_part_two_test.fields.text') }}</label>
                    <textarea class="form-control {{ $errors->has('text') ? 'is-invalid' : '' }}" style="font-size: 18px" name="text" id="text" rows="8" required>{{ old('text', $part_four->text) }}    </textarea>
                    @if($errors->has('name'))
                        <div class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.course.fields.name_helper') }}</span>
                </div>

                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.reading.part4.index',['id'=>$part_four->reading_exercise_id]) }}">
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
