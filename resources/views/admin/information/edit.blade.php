@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.information.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route( "admin.information.update" , $information->id ) }}" enctype="multipart/form-data" >
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.information.fields.link') }}</label>
                <input class="form-control {{ $errors->has('information_link') ? 'is-invalid' : '' }}" type="text" name="information_link" id="information_link" value="{{ old('information_link', $information->information_link) }}" required>
                @if($errors->has('information_link'))
                    <div class="invalid-feedback">
                        {{ $errors->first('information_link') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.information.fields.name_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="information_id">{{ trans('cruds.information.fields.type') }}</label>
                <select class="form-control  {{ $errors->has('information') ? 'is-invalid' : '' }}" name="type" id="type" required>
                    @foreach($types as $id => $type)
                        <option value="{{ $id }}" {{ old('type',$information->type) == $id ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
                @if($errors->has('type'))
                    <div class="invalid-feedback">
                        {{ $errors->first('type') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.information.fields.name_helper') }}</span>
            </div>

            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection
