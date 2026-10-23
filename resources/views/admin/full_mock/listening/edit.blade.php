@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.listening_exercise.title') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.full.listening_exercise.update",$FullExercise->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="text" name="exercise_id" hidden="" value="{{$id}}">
                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.listening.fields.title') }}</label>
                    <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" type="text" name="title" id="title" value="{{ old('title', $FullExercise->listening_exercise->name) }}" required>
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
                    <textarea class="form-control {{ $errors->has('part_one_advice') ? 'is-invalid' : '' }}" style="height: auto;max-height: 100px;min-height: 75px" type="text" rows="2" name="part_one_advice" id="part_one_advice"  required >{{ old('part_one_advice', $FullExercise->listening_exercise->part_one->advice) }}</textarea>
                    @if($errors->has('part_one_advice'))
                        <div class="invalid-feedback">
                            {{ $errors->first('part_one_advice') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening.fields.advice_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="part_one_audio">{{ trans('cruds.listening.fields.audio') }}</label>

                    <div class="d-flex gap-4 align-items-center justify-content-between">
                        <!-- File Input -->
                        <input
                            class="form-control w-50 {{ $errors->has('part_one_audio') ? 'is-invalid' : '' }}"
                            type="file"
                            accept="audio/*"
                            name="part_one_audio"
                            id="part_one_audio"
                            value="{{ old('part_one_audio') }}">

                        <!-- Audio Playback -->
                        <audio controls>
                            <source src="{{ Storage::url('public/audios/' . $FullExercise->listening_exercise->part_one->audio) }}" type="audio/mpeg">
                            Your browser does not support the audio element.
                        </audio>

                    </div>

                    <!-- Validation Error Message -->
                    @if ($errors->has('part_one_audio'))
                        <div class="invalid-feedback">
                            {{ $errors->first('part_one_audio') }}
                        </div>
                    @endif

                    <!-- Helper Text -->
                    <span class="help-block">{{ trans('cruds.listening.fields.audio_helper') }}</span>
                </div>

                <h3>{{trans('cruds.listening.title_part_two')}}</h3>
                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.listening.fields.advice') }}</label>
                    <textarea class="form-control {{ $errors->has('part_two_advice') ? 'is-invalid' : '' }}" style="height: auto;max-height: 100px;min-height: 75px" type="text" rows="2" name="part_two_advice" id="part_two_advice"  required >{{ old('part_two_advice', $FullExercise->listening_exercise->part_two->advice) }}</textarea>
                    @if($errors->has('part_two_advice'))
                        <div class="invalid-feedback">
                            {{ $errors->first('part_two_advice') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening.fields.advice_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.listening.fields.audio') }}</label>

                    <div class="d-flex gap-4 align-items-center justify-content-between">

                    <input class="form-control w-50 {{ $errors->has('part_two_audio') ? 'is-invalid' : '' }}" value="{{old('part_two_audio')}}"
                           type="file"
                           accept="part_two_audio/*"
                           name="part_two_audio"
                           id="part_two_audio">
                        <audio controls>
                            <source src="{{ Storage::url('public/audios/' . $FullExercise->listening_exercise->part_two->audio) }}" type="audio/mpeg">
                            Your browser does not support the audio element.
                        </audio>
                    </div>
                    @if($errors->has('part_two_audio'))
                        <div class="invalid-feedback">
                            {{ $errors->first('part_two_audio') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening.fields.audio_helper') }}</span>
                </div>

                <h3>{{trans('cruds.listening.title_part_three')}}</h3>
                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.listening.fields.advice') }}</label>
                    <textarea class="form-control {{ $errors->has('part_three_advice') ? 'is-invalid' : '' }}" style="height: auto;max-height: 100px;min-height: 75px" type="text" rows="2" name="part_three_advice" id="part_three_advice"  required >{{ old('part_three_advice', $FullExercise->listening_exercise->part_three->advice) }}</textarea>
                    @if($errors->has('part_three_advice'))
                        <div class="invalid-feedback">
                            {{ $errors->first('part_three_advice') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening.fields.advice_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.listening.fields.audio') }}</label>


                    <div class="d-flex gap-4 align-items-center justify-content-between">

                        <input class="form-control w-50 {{ $errors->has('part_three_audio') ? 'is-invalid' : '' }}" value="{{old('part_three_audio')}}"
                               type="file"
                               accept="part_three_audio/*"
                               name="part_three_audio"
                               id="part_three_audio">

                        <audio controls>
                            <source src="{{ Storage::url('public/audios/' . $FullExercise->listening_exercise->part_three->audio) }}" type="audio/mpeg">
                            Your browser does not support the audio element.
                        </audio>
                    </div>
                    @if($errors->has('part_three_audio'))
                        <div class="invalid-feedback">
                            {{ $errors->first('part_three_audio') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening.fields.audio_helper') }}</span>
                </div>

                <h3>{{trans('cruds.listening.title_part_four')}}</h3>
                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.listening.fields.advice') }}</label>
                    <textarea class="form-control {{ $errors->has('part_four_advice') ? 'is-invalid' : '' }}" style="height: auto;max-height: 100px;min-height: 75px" type="text" rows="2" name="part_four_advice" id="part_four_advice"  required >{{ old('part_four_advice', $FullExercise->listening_exercise->part_four->advice) }}</textarea>
                    @if($errors->has('part_four_advice'))
                        <div class="invalid-feedback">
                            {{ $errors->first('part_four_advice') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening.fields.advice_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.listening.fields.audio') }}</label>


                    <div class="d-flex gap-4 align-items-center justify-content-between">

                        <input class="form-control w-50 {{ $errors->has('part_four_audio') ? 'is-invalid' : '' }}" value="{{old('part_four_audio')}}"
                               type="file"
                               accept="part_four_audio/*"
                               name="part_four_audio"
                               id="part_four_audio">

                        <audio controls>
                            <source src="{{ Storage::url('public/audios/' . $FullExercise->listening_exercise->part_four->audio) }}" type="audio/mpeg">
                            Your browser does not support the audio element.
                        </audio>

                    </div>
                    @if($errors->has('part_four_audio'))
                        <div class="invalid-feedback">
                            {{ $errors->first('part_four_audio') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening.fields.audio_helper') }}</span>
                </div>

                <h3>{{trans('cruds.listening.title_part_five')}}</h3>
                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.listening.fields.advice') }}</label>
                    <textarea class="form-control {{ $errors->has('part_five_advice') ? 'is-invalid' : '' }}" style="height: auto;max-height: 100px;min-height: 75px" type="text" rows="2" name="part_five_advice" id="part_five_advice"  required >{{ old('part_five_advice', $FullExercise->listening_exercise->part_five->advice) }}</textarea>
                    @if($errors->has('part_five_advice'))
                        <div class="invalid-feedback">
                            {{ $errors->first('part_five_advice') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening.fields.advice_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.listening.fields.audio') }}</label>



                    <div class="d-flex gap-4 align-items-center justify-content-between">

                        <input class="form-control w-50 {{ $errors->has('part_five_audio') ? 'is-invalid' : '' }}" value="{{old('part_five_audio')}}"
                               type="file"
                               accept="part_five_audio/*"
                               name="part_five_audio"
                               id="part_five_audio">

                        <audio controls>
                            <source src="{{ Storage::url('public/audios/' . $FullExercise->listening_exercise->part_five->audio) }}" type="audio/mpeg">
                            Your browser does not support the audio element.
                        </audio>

                    </div>
                    @if($errors->has('part_five_audio'))
                        <div class="invalid-feedback">
                            {{ $errors->first('part_five_audio') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening.fields.audio_helper') }}</span>
                </div>
                <h3>{{trans('cruds.listening.title_part_six')}}</h3>
                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.listening.fields.advice') }}</label>
                    <textarea class="form-control {{ $errors->has('part_six_advice') ? 'is-invalid' : '' }}" style="height: auto;max-height: 100px;min-height: 75px" type="text" rows="2" name="part_six_advice" id="part_six_advice"  required >{{ old('part_six_advice', $FullExercise->listening_exercise->part_six->advice) }}</textarea>
                    @if($errors->has('part_six_advice'))
                        <div class="invalid-feedback">
                            {{ $errors->first('part_six_advice') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening.fields.advice_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.listening.fields.audio') }}</label>


                    <div class="d-flex gap-4 align-items-center justify-content-between">

                        <input class="form-control w-50 {{ $errors->has('part_six_audio') ? 'is-invalid' : '' }}" value="{{old('part_six_audio')}}"
                               type="file"
                               accept="part_six_audio/*"
                               name="part_six_audio"
                               id="part_six_audio">

                        <audio controls>
                            <source src="{{ Storage::url('public/audios/' . $FullExercise->listening_exercise->part_six->audio) }}" type="audio/mpeg">
                            Your browser does not support the audio element.
                        </audio>

                    </div>
                    @if($errors->has('part_six_audio'))
                        <div class="invalid-feedback">
                            {{ $errors->first('part_six_audio') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening.fields.audio_helper') }}</span>
                </div>



                <div class="form-group">
                    <label>{{ trans('cruds.listening_exercise.fields.difficulty') }}</label>{{$FullExercise->listening_exercise->difficulty}}
                    <select class="form-control {{ $errors->has('difficulty') ? 'is-invalid' : '' }}" name="difficulty" id="difficulty" onchange="change_category(this)">
                        <option value disabled >{{ trans('global.pleaseSelect') }}</option>
                        @foreach(\App\Models\ListeningExercise::DIFFICULTIES as $key => $label)

                            <option value="{{ $key }}" {{ old('difficulty', $FullExercise->listening_exercise->difficulty ) == $key ? 'selected' : '' }} >{{ $label }} </option>
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
                    <label class="required" for="time_limit">{{ trans('cruds.listening_exercise.fields.time_limit') }}</label>
                    <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" type="number" name="time_limit" id="time_limit" value="{{ old('time_limit', $FullExercise->listening_exercise->time_limit) }}" placeholder="type minutes" required >
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
