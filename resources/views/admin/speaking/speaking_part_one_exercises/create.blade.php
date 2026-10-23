@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.Speaking_exercise.title') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.speakings_exercise.part_one.store") }}" enctype="multipart/form-data">
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
                    <label class="" for="name">{{ trans('cruds.Speaking_exercise.fields.example_answer') }}</label>
                    <textarea class="form-control text-align-left {{ $errors->has('example_text') ? 'is-invalid' : '' }}"
                              name="example_answer"
                              id="name"
                              style="height: 100px; min-height: 220px; overflow-y: auto;"
                    >{{ old('example_answer') }}</textarea>
                    @if($errors->has('example_answer'))
                        <div class="invalid-feedback">
                            {{ $errors->first('example_answer') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.writing.fields.advice_helper') }}</span>
                </div>

                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.speakings_exercise.part_one.index',['id'=>$id]) }}">
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
