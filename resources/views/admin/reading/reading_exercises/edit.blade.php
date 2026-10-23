@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.reading.title') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.reading.exercise.update", [$reading_exercise->id]) }}" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <input type="hidden" name="old_part_category" id="old_part_category" value="{{$reading_exercise->part_category}}">
                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.reading.fields.title') }}</label>
                    <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', $reading_exercise->name) }}" required>
                    @if($errors->has('name'))
                        <div class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.reading.fields.name_helper') }}</span>
                </div>
                <div class="form-group">
                    <label>{{ trans('cruds.reading_exercise.fields.status') }}</label>
                    <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status" id="status">
                        <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                        @foreach(App\Models\Course::STATUS as $key => $label)
                            <option value="{{ $key }}" {{ old('free', $reading_exercise->status) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
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
                    <label>{{ trans('cruds.reading_exercise.fields.part_category') }}</label>
                    <select class="form-control {{ $errors->has('part_category') ? 'is-invalid' : '' }}" name="part_category" id="part_category" onchange="change_category(this)">
                        <option value disabled {{ old('part_category', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                        @foreach(\App\Models\ReadingExercise::PART_CATEGORIES as $key => $label)
                            <option value="{{ $key }}" {{ old('part_category',$reading_exercise->part_category) === (string) $key ? 'selected' : '' }} >{{ $label }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('part_category'))
                        <div class="invalid-feedback">
                            {{ $errors->first('part_category') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.course.fields.published_helper') }}</span>
                </div>

                <div id="part_exercise" class=" {{ old('part_category',$reading_exercise->part_category)=='6' ? 'd-none' : ''  }}">

                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.reading.fields.advice') }}</label>
                    <textarea class="form-control {{ $errors->has('advice') ? 'is-invalid' : '' }}" type="text" rows="2" name="advice" id="advice"  required >{{$reading_exercise->part_one ? $reading_exercise->part_one->advice : ($reading_exercise->part_two? $reading_exercise->part_two->advice : ($reading_exercise->part_four? $reading_exercise->part_four->advice : ($reading_exercise->part_three? $reading_exercise->part_three->advice : ''))) }}</textarea>
                    @if($errors->has('advice'))
                        <div class="invalid-feedback">
                            {{ $errors->first('advice') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.reading.fields.advice_helper') }}</span>
                </div>

                </div>

                <div id="full_exercise" class=" {{ old('part_category',$reading_exercise->part_category) == '6' ? '' : 'd-none' }}">

                    <h3>{{trans('cruds.listening.title_part_one')}}</h3>
                    <div class="form-group">
                        <label class="required" for="name">{{ trans('cruds.listening.fields.advice') }}</label>
                        <textarea class="form-control {{ $errors->has('part_one_advice') ? 'is-invalid' : '' }}" style="height: auto;max-height: 100px;min-height: 75px" type="text" rows="2" name="part_one_advice" id="advice_1"  >{{ old('part_one_advice', $reading_exercise->part_one ? $reading_exercise->part_one->advice : 'Read the text. Fill in each gap with ONE word. You must use a word which is somewhere in the rest of the text.') }}</textarea>
                        @if($errors->has('part_one_advice'))
                            <div class="invalid-feedback">
                                {{ $errors->first('part_one_advice') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.listening.fields.advice_helper') }}</span>
                    </div>

                    <h3>{{trans('cruds.listening.title_part_two')}}</h3>
                    <div class="form-group">
                        <label class="required" for="name">{{ trans('cruds.listening.fields.advice') }}</label>
                        <textarea class="form-control {{ $errors->has('part_two_advice') ? 'is-invalid' : '' }}" style="height: auto;max-height: 100px;min-height: 75px" type="text" rows="2" name="part_two_advice" id="advice_2"  >{{ old('part_two_advice', $reading_exercise->part_two ? $reading_exercise->part_two->advice :'Read the texts 7-14 and the statements A-J. Decide which text matches with the situation described in the statements. Each statement can be used ONCE only. There are TWO extra statements which you do not need to use.') }}
                    </textarea>
                        @if($errors->has('part_two_advice'))
                            <div class="invalid-feedback">
                                {{ $errors->first('part_two_advice') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.listening.fields.advice_helper') }}</span>
                    </div>

                    <h3>{{trans('cruds.listening.title_part_three')}}</h3>
                    <div class="form-group">
                        <label class="required" for="name">{{ trans('cruds.listening.fields.advice') }}</label>
                        <textarea class="form-control {{ $errors->has('part_three_advice') ? 'is-invalid' : '' }}" style="height: auto;max-height: 100px;min-height: 75px" type="text" rows="2" name="part_three_advice" id="advice_3"  >{{ old('part_three_advice', $reading_exercise->part_three ? $reading_exercise->part_three->advice :'Read the text and choose the correct heading for each paragraph from the list of headings below.There are more headings than paragraphs, so you will not use all of them. You cannot use any heading  more than once.') }}</textarea>
                        @if($errors->has('part_three_advice'))
                            <div class="invalid-feedback">
                                {{ $errors->first('part_three_advice') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.listening.fields.advice_helper') }}</span>
                    </div>

                    <h3>{{trans('cruds.listening.title_part_four')}}</h3>
                    <div class="form-group">
                        <label class="required" for="name">{{ trans('cruds.listening.fields.advice') }}</label>
                        <textarea class="form-control {{ $errors->has('part_four_advice') ? 'is-invalid' : '' }}" style="height: auto;max-height: 100px;min-height: 75px" type="text" rows="2" name="part_four_advice" id="advice_4"  >{{ old('part_four_advice',$reading_exercise->part_four ? $reading_exercise->part_four->advice : 'Read the following text for questions') }}</textarea>
                        @if($errors->has('part_four_advice'))
                            <div class="invalid-feedback">
                                {{ $errors->first('part_four_advice') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.listening.fields.advice_helper') }}</span>
                    </div>

                    <h3>{{trans('cruds.listening.title_part_five')}}</h3>
                    <div class="form-group">
                        <label class="required" for="name">{{ trans('cruds.listening.fields.advice') }}</label>
                        <textarea class="form-control {{ $errors->has('part_five_advice') ? 'is-invalid' : '' }}" style="height: auto;max-height: 100px;min-height: 75px" type="text" rows="2" name="part_five_advice" id="advice_5"  >{{ old('part_five_advice', $reading_exercise->part_five ? $reading_exercise->part_five->advice :'Read the following text for questions 30-35') }}</textarea>
                        @if($errors->has('part_five_advice'))
                            <div class="invalid-feedback">
                                {{ $errors->first('part_five_advice') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.listening.fields.advice_helper') }}</span>
                    </div>

                </div>



                <div class="form-group">
                    <label>{{ trans('cruds.listening_exercise.fields.difficulty') }}</label>
                    <select class="form-control {{ $errors->has('difficulty') ? 'is-invalid' : '' }}" name="difficulty" id="difficulty" onchange="change_category(this)">
                        <option value disabled {{ old('difficulty', null) === null ? '' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                        @foreach(\App\Models\ReadingExercise::DIFFICULTIES as $key => $label)
                            <option value="{{ $key }}" {{ old('difficulty', $reading_exercise->difficulty) == $key ? 'selected' : '' }} >{{ $label }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('difficulty'))
                        <div class="invalid-feedback">
                            {{ $errors->first('difficulty') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.course.fields.published_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="" for="time_limit">{{ trans('cruds.listening_exercise.fields.time_limit') }}</label>
                    <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" type="number" name="time_limit" id="time_limit" value="{{ old('time_limit', $reading_exercise->time_limit) }}" placeholder="type minutes"  >
                    @if($errors->has('time_limit'))
                        <div class="invalid-feedback">
                            {{ $errors->first('time_limit') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening.fields.name_helper') }}</span>
                </div>

                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.readings.exercise.index',$reading_exercise->exercise_id) }}">
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

        let advise_array=[
            `Read the text. Fill in each gap with ONE word. You must use a word which is somewhere in the rest of the text.`,
            `Read the texts 7-14 and the statements A-J. Decide which text matches with the situation described in the statements.
Each statement can be used ONCE only. There are TWO extra statements which you do not need to
use.`,
            `Read the text and choose the correct heading for each paragraph from the list of headings below.
There are more headings than paragraphs, so you will not use all of them. You cannot use any heading
more than once.
            `,
            `Read the following text for questions`,
            `Read the following text for questions 30-35`,
        ]

        function change_category(selection){

            let old_category= document.getElementById('old_part_category').value
            let full_div=document.getElementById('full_exercise')
            let part_div=document.getElementById('part_exercise')

            if (selection.value==6){

                part_div.classList.add('d-none')
                full_div.classList.remove('d-none')

            }else {

                part_div.classList.remove('d-none')
                full_div.classList.add('d-none')

                let advise_input=document.getElementById('advice')
                let key=selection.value

                if(old_category==key){
                    advise_input.value=advise_array[key-1]
                }else{
                    if (old_category=='6'){
                        let part_input = document.getElementById(`advice_${key}`);
                        advise_input.value=part_input.value;
                    }
                }
            }

        }
    </script>
@endsection
