
@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{  trans('cruds.speaking_part_one.fields.question_2')  }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.speaking.part1.2.question.store") }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="question_id" value="{{$speaking_exercise->part_one_second->id}}">
            <input type="hidden" name="exercise_id" value="{{$speaking_exercise->exercise_id}}">
            <input type="hidden" name="speaking_exercise_id" value="{{$speaking_exercise->id}}">

                <div class="form-group">
                    <label class="require" for="name">{{ trans('cruds.speaking_part_one.fields.question') }}</label>

                    <textarea class="form-control"  name="question"  style="font-size: 18px;height: auto; min-height: 50px" rows="3"> {{old('question')}} </textarea>
                </div>

            <div class="form-group">
                <label class="" for="name">{{ trans('cruds.speaking_part_one.fields.modal_answer') }}</label>

                <textarea class="form-control"  name="modal_answer"  style="font-size: 18px;height: auto; min-height: 50px" rows="6"> {{old('modal_answer')}} </textarea>
            </div>
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.speaking.fields.seconds') }}</label>
                <input class="form-control {{ $errors->has('seconds') ? 'is-invalid' : '' }}" type="number" name="seconds" id="seconds" placeholder="seconds" value="{{ old('seconds', 30) }}" required>
                @if($errors->has('seconds'))
                    <div class="invalid-feedback">
                        {{ $errors->first('seconds') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.listening.fields.name_helper') }}</span>
            </div>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.speaking.part1.index',['id'=>$speaking_exercise->id]) }} }}">
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
