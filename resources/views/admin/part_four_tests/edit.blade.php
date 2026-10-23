@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.listening_part_four.title') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.part4.map.update" , $three_four->id) }}" enctype="multipart/form-data">
                @csrf
{{--                <input type="hidden" name="question_id" value="{{$listening_exercise->listening_part_two_six_question->id}}">--}}
                <input type="hidden" name="exercise_id" value="{{$listening_exercise->exercise_id}}">
                <input type="hidden" name="listening_id" value="{{$listening_exercise->id}}">
                <div class="w-100 mb-3">

                    <img src="{{ Storage::url($listening_exercise->part_four->image ? 'public/maps/'.$listening_exercise->part_four->image : 'public/maps/default.png')}}" alt="" class="form-control w-100 " style="height: auto">
                </div>
                <div class="form-group">
                    <label class="required" for="name"> {{trans('global.choose')}} {{ trans('cruds.listening_part_four.fields.map') }}</label>
                    <div class="d-flex justify-content-between align-items-center gap-3 ">
                        <div class="d-flex gap-2 w-100">
                            <input type="file" class="form-control text-align-left {{ $errors->has('image') ? 'is-invalid' : '' }}"
                                   name="image"
                                   id="name"
                                   required value="{{old('image')}}">

                        </div>

                    </div>

                    @if($errors->has('image'))
                        <div class="invalid-feedback">
                            {{ $errors->first('image') }}
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
