@extends('layouts.admin')

@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.stepPage.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST"
                  action="{{ route('admin.stepPages.update', $stepPage->id) }}"
                  enctype="multipart/form-data">
                @method('PUT')
                @csrf

                {{-- Title --}}
                <div class="form-group">
                    <label class="required" for="title">{{ trans('cruds.stepPage.fields.title') }}</label>
                    <input type="text" id="title" name="title"
                           class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}"
                           value="{{ old('title', $stepPage->title) }}" required>
                    @if($errors->has('title'))
                        <div class="invalid-feedback">{{ $errors->first('title') }}</div>
                    @endif
                    <span class="help-block">{{ trans('cruds.stepPage.fields.title_helper') }}</span>
                </div>

                {{-- Description --}}
                <div class="form-group">
                    <label class="required" for="description">{{ trans('cruds.stepPage.fields.description') }}</label>
                    <textarea id="description" name="description" rows="6"
                              class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}"
                              required>{{ old('description', $stepPage->description) }}</textarea>
                    @if($errors->has('description'))
                        <div class="invalid-feedback">{{ $errors->first('description') }}</div>
                    @endif
                    <span class="help-block">{{ trans('cruds.stepPage.fields.description_helper') }}</span>
                </div>

                {{-- Current image preview --}}
                <div class="form-group">
                    <label>{{ trans('cruds.stepPage.fields.image') }}</label>
                    <div class="mb-2">
                        @if($stepPage->image)
                            <a href="{{ asset('storage/'.$stepPage->image) }}" target="_blank" rel="noopener">
                                <img src="{{ asset('storage/'.$stepPage->image) }}"
                                     alt="{{ $stepPage->title }}"
                                     class="img-thumbnail"
                                     style="max-height:120px; object-fit:cover;">
                            </a>
                        @else
                            <span class="text-muted">{{ __('No image') }}</span>
                        @endif
                    </div>

                    {{-- Upload new image (optional) --}}
                    <input type="file" id="image" name="image"
                           class="form-control {{ $errors->has('image') ? 'is-invalid' : '' }}"
                           accept="image/*">
                    @if($errors->has('image'))
                        <div class="invalid-feedback">{{ $errors->first('image') }}</div>
                    @endif
                    <small class="form-text text-muted">
                        {{ __('Upload to replace the current image (optional).') }}
                    </small>
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
