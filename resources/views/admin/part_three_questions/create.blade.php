@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{trans('cruds.listening_part_three.add_title') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.part3.speaker.store") }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="listening_part_three_four_id" value="{{$listening_exercise->listening_part_three_four->id}}">
            <input type="hidden" name="exercise_id" value="{{$listening_exercise->exercise_id}}">
            <input type="hidden" name="listening_id" value="{{$listening_exercise->id}}">

            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.listening_part_three.add_title') }}</label>
                <div class="d-flex justify-content-between align-items-center gap-3 ">
                    <div class="d-flex gap-2 w-100">
                        <input class="form-control text-align-left {{ $errors->has('answer') ? 'is-invalid' : '' }}" value="{{ old('title') }}"
                               name="title"
                               id="title"
                               required>

                    </div>

                </div>

                @if($errors->has('title'))
                    <div class="invalid-feedback">
                        {{ $errors->first('title') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.listening_part_one_test.fields.name_helper') }}</span>
            </div>
            <div class="form-group">
                <label>{{ trans('cruds.listening_part_three.letter') }}</label>
                <select class="form-control {{ $errors->has('correct_answer_id') ? 'is-invalid' : '' }}" name="correct_answer_id"  id="count_number">
                    <option value disabled {{ old('correct_answer_id', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach($listening_exercise->listening_part_three_four->part_three_four_answers as $key => $answer)
                        <option value="{{ $answer->id }}" {{ old('correct_answer_id') === (string) $answer->id ? 'selected' : '' }}>{{ $answer->answer }}</option>
                    @endforeach
                </select>
                @if($errors->has('correct_answer_id'))
                    <div class="invalid-feedback">
                        {{ $errors->first('correct_answer_id') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.listening_part_two_test.fields.id_helper') }}</span>
            </div>

            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.listenings.part3.index',['id'=>$listening_exercise->id,'exercise_id'=>$listening_exercise->exercise_id]) }} }}">
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
