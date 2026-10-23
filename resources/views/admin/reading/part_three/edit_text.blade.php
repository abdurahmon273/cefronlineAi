@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{trans('cruds.reading_part_two.title_text') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.part3.text.update", $text->id) }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="reading_part_two_three_id" value="{{$reading_exercise->part_three->id}}">
                <input type="hidden" name="exercise_id" value="{{$reading_exercise->exercise_id}}">
                <input type="hidden" name="reading_id" value="{{$reading_exercise->id}}">


                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.reading_part_one.fields.text') }}</label>
                    <div class="d-flex justify-content-between align-items-center gap-3 ">
                        <div class="d-flex gap-2 w-100">
                            <textarea class="form-control text-align-left  {{ $errors->has('text') ? 'is-invalid' : '' }}" style="font-size: 18px"   name="text" id="text" rows="8"  required > {{ old('text',$text->text) }}</textarea>
                        </div>

                    </div>

                    @if($errors->has('text'))
                        <div class="invalid-feedback">
                            {{ $errors->first('text') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening_part_one_test.fields.name_helper') }}</span>
                </div>
                <div class="form-group">
                    <label>{{ trans('cruds.reading_part_two.fields.correct_answer') }}</label>
                    <select class="form-control {{ $errors->has('correct_answer_id') ? 'is-invalid' : '' }}" name="correct_answer_id"  id="count_number">
                        <option value disabled {{ old('correct_answer_id', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                        @foreach($answers as $key => $answer)
                            <option value="{{ $answer->id }}" {{ old('correct_answer_id',$text->correct_answer_id) ===  $answer->id ? 'selected' : '' }}>{{ $answer->answer }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('correct_answer_id'))
                        <div class="invalid-feedback">
                            {{ $errors->first('correct_answer_id') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening_part_two_test.fields.id_helper') }}</span>
                </div>

                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.reading.part3.index',['id'=>$reading_exercise->id]) }}">
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
