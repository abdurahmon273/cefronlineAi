@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.video.title') }}
        </div>

        <div class="card-body">
            <form method="post" action="{{route("admin.lessons.store2")}}" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="form-group col">
                    <label class="required"
                           for="link">{{ trans('cruds.video.fields.link') }} {{ trans('cruds.video.title')  }}</label>
                    <input class="form-control {{ $errors->has('link') ? 'is-invalid' : '' }}" type="text"
                           id="link" value="{{ old('link', '') }}" name="link" required >
                    @if($errors->has('link'))
                        <div class="invalid-feedback">
                            {{ $errors->first('link') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.video.fields.link_helper') }}</span>
                </div>
                <input type="text" name="lesson_id" id="" hidden="" value="{{$lesson_id}}">
                <div class="form-group col">
                    <label for="description">{{ trans('cruds.video.fields.description') }}</label>
                    <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}"
                              id="description"
                              name="description" required >{{ old('description') }}</textarea>
                    @if($errors->has('description'))
                        <div class="invalid-feedback">
                            {{ $errors->first('description') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.video.fields.description_helper') }}</span>
                </div>
                <div class="col">
                    <label for="files">{{ trans('cruds.video.fields.files') }}</label>
                    <div class="needsclick dropzone {{ $errors->has('files2') ? 'is-invalid' : '' }}"
                         id="files-dropzone2">
                    </div>
                    <span class="help-block">{{ trans('cruds.video.fields.files_helper') }}</span>
                </div>

                <hr>
                <div class="form-group d-flex justify-content-between">
                    <a class="btn btn-dark" href="{{route('admin.lessons.index')}}" type="submit">
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

    {{--video--}}
    <script>
        var uploadedFilesMap2 = {}
        Dropzone.options.filesDropzone2 = {
            url: '{{ route('admin.videos.storeMedia') }}',
            maxFilesize: 200, // MB
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            params: {
                size: 200
            },
            success: function (file, response) {
                $('form').append('<input type="hidden" name="files2[]" value="' + response.name + '">')
                uploadedFilesMap2[file.name] = response.name
            },
            removedfile: function (file) {
                file.previewElement.remove()
                var name = ''
                if (typeof file.file_name !== 'undefined') {
                    name = file.file_name
                } else {
                    name = uploadedFilesMap2[file.name]
                }
                $('form').find('input[name="files2[]"][value="' + name + '"]').remove()
            },
            init: function () {
                @if(isset($video) && $video->files)
                @dd($video->files)
                var files =
                    {!! json_encode($video->files) !!}
                    for(
                var i
            in
                files
            )
                {
                    var file = files[i]
                    this.options.addedfile.call(this, file)
                    file.previewElement.classList.add('dz-complete')
                    $('form').append('<input type="hidden" name="files2[]" value="' + file.file_name + '">')
                }
                @endif
            },
            error: function (file, response) {
                if ($.type(response) === 'string') {
                    var message = response //dropzone sends it's own error messages in string
                } else {
                    var message = response.errors.file
                }
                file.previewElement.classList.add('dz-error')
                _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
                _results = []
                for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                    node = _ref[_i]
                    _results.push(node.textContent = message)
                }

                return _results
            }
        }
    </script>

@endsection
