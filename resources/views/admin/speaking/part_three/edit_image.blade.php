
@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{  trans('cruds.speaking_part_one.fields.image')  }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.speaking.part2.image.update",['id'=>$image->id]) }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="question_id" value="{{$speaking_exercise->part_two->id}}">
            <input type="hidden" name="exercise_id" value="{{$speaking_exercise->exercise_id}}">
            <input type="hidden" name="speaking_exercise_id" value="{{$speaking_exercise->id}}">

            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.speaking.fields.image') }}</label>
                <input class="form-control {{ $errors->has('image') ? 'is-invalid' : '' }}" type="file" name="image" id="image" placeholder="choose image" value="{{ old('image', 30) }}" required>
                @if($errors->has('image'))
                    <div class="invalid-feedback">
                        {{ $errors->first('image') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.listening.fields.name_helper') }}</span>
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
