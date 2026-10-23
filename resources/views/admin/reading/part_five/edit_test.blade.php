@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.reading_part_five.fields.test') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.part5.test.update" ,$test->id) }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="reading_part_four_id" value="{{$reading_exercise->part_five->id}}">
            <input type="hidden" name="exercise_id" value="{{$reading_exercise->exercise_id}}">
            <input type="hidden" name="reading_id" value="{{$reading_exercise->id}}">

            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.reading_part_four.fields.question') }}</label>
                <div class="d-flex justify-content-between align-items-center gap-3 ">
                    <div class="d-flex gap-2 w-100">
                        <textarea class="form-control text-align-left {{ $errors->has('question') ? 'is-invalid' : '' }}" name="question" id="question" style="height: 50px; min-height: 50px; overflow-y: auto;" required>{{ old('question' ,$test->question) }}</textarea>
                    </div>
                </div>
                </div>
                <div class="form-group mt-3">
                    <label class="required" for="name">{{ trans('cruds.reading_part_four.fields.answer1') }}</label>
                    <div class="d-flex justify-content-between align-items-center gap-3 ">
                        <div class="d-flex gap-2 w-75">
                            <textarea class="form-control text-align-left {{ $errors->has('answer_1') ? 'is-invalid' : '' }}"
                                      name="answer_1"
                                      id="name"
                                      style="height: 50px; min-height: 50px; overflow-y: auto;"
                                      required>{{ old('answer_1',$test->answer_1) }}</textarea>

                        </div>
                        <div class="d-flex w-25 justify-content-center">
                            <input type="radio" style="transform: scale(1.5);" name="correct_answer" value="1"  {{$test->correct_answer=='1' ? 'checked' : ''}} id="">
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
                    <label class="required" for="name">{{ trans('cruds.reading_part_four.fields.answer2') }}</label>
                    <div class="d-flex justify-content-between align-items-center gap-3 ">
                        <div class="d-flex gap-2 w-75">
                            <textarea class="form-control text-align-left {{ $errors->has('answer_2') ? 'is-invalid' : '' }}"
                                      name="answer_2"
                                      id="name"
                                      style="height: 50px; min-height: 50px; overflow-y: auto;"
                                      required>{{ old('answer_2',$test->answer_2) }}</textarea>

                        </div>
                        <div class="d-flex w-25 justify-content-center">
                            <input type="radio" style="transform: scale(1.5);" name="correct_answer" value="2" {{$test->correct_answer=='2' ? 'checked' : ''}} id="">
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
                    <label class="required" for="name">{{ trans('cruds.reading_part_four.fields.answer3') }}</label>
                    <div class="d-flex justify-content-between align-items-center gap-3 ">
                        <div class="d-flex gap-2 w-75">
                            <textarea class="form-control text-align-left {{ $errors->has('answer_3') ? 'is-invalid' : '' }}"
                                      name="answer_3"
                                      id="name"
                                      style="height: 50px; min-height: 50px; overflow-y: auto;"
                                      required>{{ old('answer_3',$test->answer_3) }}</textarea>

                        </div>
                        <div class="d-flex w-25 justify-content-center">
                            <input type="radio" style="transform: scale(1.5);" name="correct_answer" value="3" {{$test->correct_answer=='3' ? 'checked' : ''}} id="">
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
                    <label class="" for="name">{{ trans('cruds.reading_part_four.fields.answer4') }}</label>
                    <div class="d-flex justify-content-between align-items-center gap-3 ">
                        <div class="d-flex gap-2 w-75">
                            <textarea class="form-control text-align-left {{ $errors->has('answer_4') ? 'is-invalid' : '' }}"
                                      name="answer_4"
                                      id="name"
                                      style="height: 50px; min-height: 50px; overflow-y: auto;"
                                      >{{ old('answer_4',$test->answer_4) }}</textarea>

                        </div>
                        <div class="d-flex w-25 justify-content-center">
                            <input type="radio" style="transform: scale(1.5);" name="correct_answer" value="4" {{$test->correct_answer=='4' ? 'checked' : ''}} id="">
                        </div>
                    </div>

                    @if($errors->has('answer_4'))
                        <div class="invalid-feedback">
                            {{ $errors->first('answer_4') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening_part_one_test.fields.name_helper') }}</span>
                </div>

            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.reading.part5.index',['id'=>$reading_exercise->id]) }}">
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
