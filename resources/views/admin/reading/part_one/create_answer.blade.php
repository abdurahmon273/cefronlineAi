@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ $reading_exercise->part_one->type=='0' ? trans('cruds.reading_part_one.fields.answer'):trans('cruds.listening_part_two_test.title_1')  }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.part1.answer.store") }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="question_id" value="{{$reading_exercise->part_one->id}}">
                <input type="hidden" name="exercise_id" value="{{$reading_exercise->exercise_id}}">
                <input type="hidden" name="reading_id" value="{{$reading_exercise->id}}">

                <div class="form-group">
                    <label class="require" for="name">{{ trans('cruds.listening_part_two_test.fields.text') }}</label>

                    <textarea class="form-control" disabled style="font-size: 18px"
                              rows="12"> {{$updatedText}} </textarea>
                </div>
                <div class="form-group">
                    <label class="required"
                           for="name">{{ trans('cruds.listening_part_two_test.fields.correct_answer') }}</label>
                    <div class="d-flex justify-content-between align-items-center gap-3 ">
                        <div class="d-flex gap-2 w-100">
                            <input
                                class="form-control text-align-left {{ $errors->has('correct_answer') ? 'is-invalid' : '' }}"
                                name="correct_answer"
                                id="name"
                                required>

                        </div>

                    </div>

                    @if($errors->has('correct_answer'))
                        <div class="invalid-feedback">
                            {{ $errors->first('correct_answer') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening_part_one_test.fields.name_helper') }}</span>
                </div>
                <div class="form-group mt-2">
                    <label class=""
                           for="name">{{ trans('cruds.listening_part_two_test.fields.extra_answer') }}</label>
                    <div class="d-flex justify-content-between align-items-center gap-3 ">
                        <div class="d-flex gap-2 w-100">
                            <input
                                class="form-control text-align-left {{ $errors->has('extra_answer') ? 'is-invalid' : '' }}"
                                name="extra_answer"
                                id="name" value="{{old('extra_answer')}}"
                            >

                        </div>

                    </div>

                    @if($errors->has('extra_answer'))
                        <div class="invalid-feedback">
                            {{ $errors->first('extra_answer') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening_part_one_test.fields.name_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required">{{ trans('cruds.reading_part_one.fields.count_number') }}</label>
                    <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="count_number"
                            id="count_number">
                        <option value
                                disabled {{ old('count_number', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                        @foreach($array as $key => $label)
                            <option
                                value="{{ $key }}" {{ old('count_number') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('count_number'))
                        <div class="invalid-feedback">
                            {{ $errors->first('count_number') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening_part_two_test.fields.id_helper') }}</span>
                </div>

                <div class="form-group">
                    <a class="btn btn-default"
                       href="{{ route('admin.reading.part1.index',['id'=>$reading_exercise->id]) }} }}">
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
