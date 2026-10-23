@extends('layouts.admin')

@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.stepPage.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.stepPages.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label class="required" for="title">{{ trans('cruds.stepPage.fields.title') }}</label>
                    <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}"
                           type="text" name="title" id="title" value="{{ old('title') }}" required>
                    @if($errors->has('title'))
                        <div class="invalid-feedback">{{ $errors->first('title') }}</div>
                    @endif
                    <span class="help-block">{{ trans('cruds.stepPage.fields.title_helper') }}</span>
                </div>

                <div class="form-group">
                    <label class="required" for="description">{{ trans('cruds.stepPage.fields.description') }}</label>
                    <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}"
                              name="description" id="description" rows="5" required>{{ old('description') }}</textarea>
                    @if($errors->has('description'))
                        <div class="invalid-feedback">{{ $errors->first('description') }}</div>
                    @endif
                    <span class="help-block">{{ trans('cruds.stepPage.fields.description_helper') }}</span>
                </div>

                <div class="form-group">
                    <label class="required" for="image">{{ trans('cruds.stepPage.fields.image') }}</label>
                    <input class="form-control {{ $errors->has('image') ? 'is-invalid' : '' }}"
                           type="file" name="image" id="image" accept="image/*" required>
                    @if($errors->has('image'))
                        <div class="invalid-feedback">{{ $errors->first('image') }}</div>
                    @endif
                    <span class="help-block">{{ trans('cruds.stepPage.fields.image_helper') }}</span>
                </div>

                <div class="form-group">
                    <button class="btn btn-danger" type="submit">{{ trans('global.save') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
