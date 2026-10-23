@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.reading_exercise.title') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.full.reading_exercise.update",$FullExercise->id) }}" enctype="multipart/form-data" >
                @csrf
                @method('PUT')
                <input type="text" name="exercise_id" hidden="" value="{{$id}}">
                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.listening.fields.title') }}</label>
                    <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" type="text" name="title" id="title" value="{{ old('title', $FullExercise->reading_exercise->name) }}" required>
                    @if($errors->has('title'))
                        <div class="invalid-feedback">
                            {{ $errors->first('title') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening.fields.name_helper') }}</span>
                </div>

                <h3>{{trans('cruds.listening.title_part_one')}}</h3>
                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.listening.fields.advice') }}</label>
                    <textarea class="form-control {{ $errors->has('part_one_advice') ? 'is-invalid' : '' }}" style="height: auto;max-height: 100px;min-height: 75px" type="text" rows="2" name="part_one_advice" id="part_one_advice"  required >{{ old('part_one_advice', $FullExercise->reading_exercise->part_one->advice) }}</textarea>
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
                    <textarea class="form-control {{ $errors->has('part_two_advice') ? 'is-invalid' : '' }}" style="height: auto;max-height: 100px;min-height: 75px" type="text" rows="2" name="part_two_advice" id="part_two_advice"  required >{{ old('part_two_advice', $FullExercise->reading_exercise->part_two->advice) }}</textarea>
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
                    <textarea class="form-control {{ $errors->has('part_three_advice') ? 'is-invalid' : '' }}" style="height: auto;max-height: 100px;min-height: 75px" type="text" rows="2" name="part_three_advice" id="part_three_advice"  required >{{ old('part_three_advice', $FullExercise->reading_exercise->part_three->advice) }}</textarea>
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
                    <textarea class="form-control {{ $errors->has('part_four_advice') ? 'is-invalid' : '' }}" style="height: auto;max-height: 100px;min-height: 75px" type="text" rows="2" name="part_four_advice" id="part_four_advice"  required >{{ old('part_four_advice', $FullExercise->reading_exercise->part_four->advice) }}</textarea>
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
                    <textarea class="form-control {{ $errors->has('part_five_advice') ? 'is-invalid' : '' }}" style="height: auto;max-height: 100px;min-height: 75px" type="text" rows="2" name="part_five_advice" id="part_five_advice"  required >{{ old('part_five_advice', $FullExercise->reading_exercise->part_five->advice) }}</textarea>
                    @if($errors->has('part_five_advice'))
                        <div class="invalid-feedback">
                            {{ $errors->first('part_five_advice') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening.fields.advice_helper') }}</span>
                </div>



                <div class="form-group">
                    <label>{{ trans('cruds.listening_exercise.fields.difficulty') }}</label>{{$FullExercise->reading_exercise->difficulty}}
                    <select class="form-control {{ $errors->has('difficulty') ? 'is-invalid' : '' }}" name="difficulty" id="difficulty" onchange="change_category(this)">
                        <option value disabled >{{ trans('global.pleaseSelect') }}</option>
                        @foreach(\App\Models\ListeningExercise::DIFFICULTIES as $key => $label)

                            <option value="{{ $key }}" {{ old('difficulty', $FullExercise->reading_exercise->difficulty ) == $key ? 'selected' : '' }} >{{ $label }} </option>
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
                    <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" type="number" name="time_limit" id="time_limit" value="{{ old('time_limit', $FullExercise->reading_exercise->time_limit) }}" placeholder="type minutes"  >
                    @if($errors->has('time_limit'))
                        <div class="invalid-feedback">
                            {{ $errors->first('time_limit') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening.fields.name_helper') }}</span>
                </div>

                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.full_exercise.more.index',['id'=>$FullExercise->id]) }}">
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

@endsection
