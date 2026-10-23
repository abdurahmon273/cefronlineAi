
@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{  trans('cruds.speaking_part_one.fields.question')  }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.speaking.part2.question.update",['id'=>$question->id]) }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="question_id" value="{{$speaking_exercise->part_two->id}}">
                <input type="hidden" name="exercise_id" value="{{$speaking_exercise->exercise_id}}">
                <input type="hidden" name="speaking_exercise_id" value="{{$speaking_exercise->id}}">

                <div class="form-group">
                    <label class="require" for="name">{{ trans('cruds.speaking_part_one.fields.question') }}</label>
                    <textarea class="form-control"  name="question"  style="font-size: 18px;height: auto; min-height: 50px" rows="3"> {{old('question',$question->question)}} </textarea>
                    @if($errors->has('question'))
                        <div class="invalid-feedback">
                            {{ $errors->first('question') }}
                        </div>
                    @endif
                </div>

                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.speaking.part2.index',['id'=>$speaking_exercise->id]) }} }}">
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
