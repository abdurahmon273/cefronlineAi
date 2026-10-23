@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.listening_part_one_test.title') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.listenings.part1.update",$test->id) }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="listening_part_one_fife_question_id" value="{{$question_id}}">
                <input type="hidden" name="exercise_id" value="{{$exercise_id}}">
                <input type="hidden" name="listening_id" value="{{$listening_id}}">

                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.listening_part_one_test.fields.answer1') }}</label>
                    <div class="d-flex justify-content-between align-items-center gap-3 ">
                        <div class="d-flex gap-2 w-75">
                            <textarea class="form-control text-align-left {{ $errors->has('answer_1') ? 'is-invalid' : '' }}"
                                      name="answer_1"
                                      id="name"
                                      style="height: 50px; min-height: 50px; overflow-y: auto;"
                                      required>{{ old('answer_1') ?? $test->answer_1 }}</textarea>

                        </div>
                        <div class="d-flex w-25 justify-content-center">
                            <input type="radio" style="transform: scale(1.5);" name="correct_answer" {{$test->correct_answer==1 ? 'checked': ''}}  value="1" id="">
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
                    <label class="required" for="name">{{ trans('cruds.listening_part_one_test.fields.answer2') }}</label>
                    <div class="d-flex justify-content-between align-items-center gap-3 ">
                        <div class="d-flex gap-2 w-75">
                            <textarea class="form-control text-align-left {{ $errors->has('answer_2') ? 'is-invalid' : '' }}"
                                      name="answer_2"
                                      id="name"
                                      style="height: 50px; min-height: 50px; overflow-y: auto;"
                                      required>{{ old('answer_2') ?? $test->answer_2 }}</textarea>

                        </div>
                        <div class="d-flex w-25 justify-content-center">
                            <input type="radio" style="transform: scale(1.5);" name="correct_answer" {{$test->correct_answer==2 ? 'checked': ''}}  value="2" id="">
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
                    <label class="required" for="name">{{ trans('cruds.listening_part_one_test.fields.answer3') }}</label>
                    <div class="d-flex justify-content-between align-items-center gap-3 ">
                        <div class="d-flex gap-2 w-75">
                            <textarea class="form-control text-align-left {{ $errors->has('answer_3') ? 'is-invalid' : '' }}"
                                      name="answer_3"
                                      id="name"
                                      style="height: 50px; min-height: 50px; overflow-y: auto;"
                                      required>{{ old('answer_3')  ?? $test->answer_3 }}</textarea>

                        </div>
                        <div class="d-flex w-25 justify-content-center">
                            <input type="radio" style="transform: scale(1.5);" name="correct_answer" {{$test->correct_answer==3 ? 'checked': ''}} value="3" id="">
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
                    <a class="btn btn-default" href="{{ route('admin.listenings.part1.index',['id'=>$listening_id,'exercise_id'=>$exercise_id]) }}">
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
