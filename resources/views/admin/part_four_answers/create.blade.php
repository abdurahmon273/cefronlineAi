@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{trans('cruds.listening_part_four.add_title1') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.part4.location.store") }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="listening_part_three_four_id" value="{{$listening_exercise->part_four->id}}">
            <input type="hidden" name="exercise_id" value="{{$listening_exercise->exercise_id}}">
            <input type="hidden" name="listening_id" value="{{$listening_exercise->id}}">

            <div class="form-group">
                <label class="required" for="letter_key">{{ trans('cruds.listening_part_four.fields.letter') }}</label>
                <select class="form-control {{ $errors->has('letter_key') ? 'is-invalid' : '' }}" name="letter_key"  id="letter_key">
                    <option value disabled {{ old('letter_key', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach($array as $key => $label)
                        <option value="{{ $key }}" {{ old('letter_key') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('letter_key'))
                    <div class="invalid-feedback">
                        {{ $errors->first('letter_key') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.listening_part_two_test.fields.id_helper') }}</span>
            </div>
            <div class="form-group">
                    <label for="name">{{ trans('cruds.listening_part_four.fields.title') }}</label>
                    <div class="d-flex justify-content-between align-items-center gap-3 ">
                        <div class="d-flex gap-2 w-100">
                            <input class="form-control text-align-left " value="{{old('answer')}}" name="answer" id="answer">

                        </div>

                    </div>

                    @if($errors->has('answer'))
                        <div class="invalid-feedback">
                            {{ $errors->first('answer') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.listening_part_one_test.fields.name_helper') }}</span>
                </div>

            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.listenings.part4.index',['id'=>$listening_exercise->id,'exercise_id'=>$listening_exercise->exercise_id]) }} }}">
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
