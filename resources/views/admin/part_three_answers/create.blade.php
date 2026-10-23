@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{trans('cruds.listening_part_three.add_title1') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.part3.answer.store") }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="listening_part_three_four_id" value="{{$listening_exercise->listening_part_three_four->id}}">
            <input type="hidden" name="exercise_id" value="{{$listening_exercise->exercise_id}}">
            <input type="hidden" name="listening_id" value="{{$listening_exercise->id}}">

            <div class="form-group">
                <label>{{ trans('cruds.listening_part_three.letter') }}</label>
                <select class="form-control {{ $errors->has('letter_key') ? 'is-invalid' : '' }}" name="letter_key" onchange="change_letter(this)" id="count_number">
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
                    <label class="required" for="name">{{ trans('cruds.listening_part_three.add_title1') }}</label>
                    <div class="d-flex justify-content-between align-items-center gap-3 ">
                        <div class="d-flex gap-2 w-100">
                            <input class="form-control text-align-left {{ $errors->has('answer') ? 'is-invalid' : '' }}" value="{{old('answer')}}"
                                      name="answer"
                                      id="answer"
                                      required>

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
    let array=['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];

  function change_letter(selection){
      let input =document.getElementById('answer');
      let currentValue = input.value;
      if (input.value){
          let newValue = currentValue.replace(/^[A-Z]\)\s*/, '');
          input.value = array[selection.value] + ')' + newValue;
      }else{
          input.value=array[selection.value]+')'
      }
      console.log(array[selection.value])
  }
</script>
@endsection
