@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.reading_part_four.title_TFNg') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.part4.tfn.update",$test->id) }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="reading_part_four_id" value="{{$test->part_four->id}}">
                <input type="hidden" name="exercise_id" value="{{$reading_exercise->exercise_id}}">
                <input type="hidden" name="reading_id" value="{{$reading_exercise->id}}">

                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.listening_part_five_test.fields.question') }}</label>
                    <div class="d-flex justify-content-between align-items-center gap-3 ">
                        <div class="d-flex gap-2 w-100">
                            <textarea class="form-control text-align-left {{ $errors->has('question') ? 'is-invalid' : '' }}" name="question" id="question" style="height: 50px; min-height: 50px; overflow-y: auto;" required>{{ old('question',$test->question) }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="form-group mt-3">
                    <label class="required" for="name">{{ trans('cruds.listening_part_five_test.fields.answer1') }}</label>
                    <div class="d-flex justify-content-between align-items-center gap-3 ">
                        <div class="d-flex gap-2 w-25">
                            <input type="hidden" name="answer_1"  value="True">
                            <h5>True</h5>

                        </div>
                        <div class="d-flex w-50 justify-content-start">
                            <input type="radio" style="transform: scale(1.5);" name="correct_answer" {{$test->correct_answer==1 ? 'checked': ''}} value="1" id="">
                        </div>
                    </div>

                    @if($errors->has('answer_1'))
                        <div class="invalid-feedback">
                            {{ $errors->first('answer_1') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening_part_one_test.fields.name_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.listening_part_five_test.fields.answer2') }}</label>
                    <div class="d-flex justify-content-between align-items-center gap-3 ">
                        <div class="d-flex gap-2 w-25">
                            <input type="hidden" name="answer_2"  value="False">
                            <h5>False</h5>

                        </div>
                        <div class="d-flex w-50 justify-content-start">
                            <input type="radio" style="transform: scale(1.5);" name="correct_answer" value="2" {{$test->correct_answer==2 ? 'checked': ''}} id="">
                        </div>
                    </div>

                    @if($errors->has('answer_2'))
                        <div class="invalid-feedback">
                            {{ $errors->first('answer_1') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening_part_one_test.fields.name_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.listening_part_five_test.fields.answer3') }}</label>
                    <div class="d-flex justify-content-between align-items-center gap-3 ">
                        <div class="d-flex gap-2 w-25">
                            <input type="hidden" name="answer_3"  value="Not Given">
                            <h5>Not Given</h5>

                        </div>
                        <div class="d-flex w-50 justify-content-start">
                            <input type="radio" style="transform: scale(1.5);" name="correct_answer" value="3" {{$test->correct_answer==3 ? 'checked': ''}} id="">
                        </div>
                    </div>

                    @if($errors->has('answer_3'))
                        <div class="invalid-feedback">
                            {{ $errors->first('answer_3') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening_part_one_test.fields.name_helper') }}</span>
                </div>

                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.reading.part4.index',['id'=>$reading_exercise->id]) }}">
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
