@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.Speaking_exercise.title') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.speakings_exercise.part_three.store") }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="text" name="exercise_id" hidden="" value="{{$id}}">
                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.writing.fields.title') }}</label>
                    <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" type="text" name="title" id="title" value="{{ old('title', '') }}" required>
                    @if($errors->has('title'))
                        <div class="invalid-feedback">
                            {{ $errors->first('title') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.writing.fields.name_helper') }}</span>
                </div>
                <div class="form-group">
                    <label>{{ trans('cruds.reading_exercise.fields.status') }}</label>
                    <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status" id="status">
                        <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                        @foreach(App\Models\Course::STATUS as $key => $label)
                            <option value="{{ $key }}" {{ old('free', '0') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
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
                    <label class="required" for="name">{{ trans('cruds.speaking.fields.seconds') }}</label>
                    <input class="form-control {{ $errors->has('seconds') ? 'is-invalid' : '' }}" type="number" name="seconds" id="seconds" placeholder="seconds" value="{{ old('seconds', 120) }}" required>
                    @if($errors->has('seconds'))
                        <div class="invalid-feedback">
                            {{ $errors->first('seconds') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening.fields.name_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="require" for="name">{{ trans('cruds.speaking.fields.theme') }}</label>
                    <textarea class="form-control"  name="theme"  style="font-size: 18px;height: auto; min-height: 50px" rows="3"> {{old('theme')}} </textarea>

                </div>

                <div class="form-group">
                    <label class="" for="name">{{ trans('cruds.Speaking_exercise.fields.modal_answer') }}</label>
                    <textarea class="form-control text-align-left {{ $errors->has('modal_answer') ? 'is-invalid' : '' }}"
                              name="modal_answer"
                              id="name"
                              style="height: 100px; min-height: 220px; overflow-y: auto;"
                    >{{ old('modal_answer') }}</textarea>

                </div>

                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.speakings_exercise.part_three.index',['id'=>$id]) }}">
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


        // let advise_array=[
        //     `Write about 150 words in an appropriate format and style.`,
        //     `Write at least 250 words in an appropriate style and format.`,
        //
        // ]
        //
        // function change_category(selection){
        //     let advise_input=document.getElementById('advice')
        //     let word_count=document.getElementById('word_count')
        //     let bullet=document.getElementById('bullet_point')
        //     let key=selection.value
        //     advise_input.value=advise_array[key-1]
        //     if (key==1){
        //         word_count.value=150;
        //         bullet.classList.remove('d-none')
        //     }else {
        //         word_count.value=250;
        //         bullet.classList.add('d-none')
        //
        //     }
        // }
    </script>
@endsection
