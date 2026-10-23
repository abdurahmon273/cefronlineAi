@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }}  {{trans('cruds.speaking_part_three.fields.question_text')  }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.part3.speaking.text.update", [$question->id] ) }}" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.listening_part_two_test.fields.text') }}</label>
                    <textarea class="form-control {{ $errors->has('text') ? 'is-invalid' : '' }}" style="font-size: 18px" name="text" id="text" rows="8" required>{{ old('text', $question->question_text) }}    </textarea>
                    @if($errors->has('name'))
                        <div class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.course.fields.name_helper') }}</span>
                </div>

                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.speaking.part3.index',['id'=>$question->speaking_exercise_id]) }}">
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
