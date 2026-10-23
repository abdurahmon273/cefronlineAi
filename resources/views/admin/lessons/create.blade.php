@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.lesson.title_singular') }}
        </div>

        <div class="card-body">
            <form method="post" action="{{route("admin.lessons.store")}}" enctype="multipart/form-data">
                @csrf
                <div class="form-group col">
                    <label class="required" for="name">{{ trans('cruds.lesson.fields.name') }}</label>
                    <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name"
                           id="name" value="{{ old('name', '') }}" required>
                    @if($errors->has('name'))
                        <div class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.lesson.fields.name_helper') }}</span>
                </div>
                    <input type="text" name="course_id" hidden="" value="{{$course_id}}">
                <div class="form-group col">
                    <label for="files">{{ trans('cruds.lesson.fields.image') }}</label>
                    <div class="needsclick dropzone {{ $errors->has('files') ? 'is-invalid' : '' }}"
                         id="files-dropzone">
                    </div>
                    @if($errors->has('files'))
                        <div class="invalid-feedback">
                            {{ $errors->first('files') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.lesson.fields.files_helper') }}</span>
                </div>
                <div class="form-group">
                    <label>{{ trans('cruds.course.fields.status') }}</label>
                    <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status" id="status">
                        <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                        @foreach(App\Models\Lesson::STATUS as $key => $label)
                            <option value="{{ $key }}" {{ old('free', '0') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('status'))
                        <div class="invalid-feedback">
                            {{ $errors->first('status') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.course.fields.published_helper') }}</span>
                </div>
                <div class="form-group">
                    <a class="btn btn-dark" href="{{route('admin.lessons.index2',['id'=>$course_id])}}" type="submit">
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
        var uploadedFilesMap = {}
        Dropzone.options.filesDropzone = {
            url: '{{ route('admin.lessons.storeMedia') }}',
            maxFilesize: 200, // MB
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            params: {
                size: 200
            },
            success: function (file, response) {
                $('form').append('<input type="hidden" name="files[]" value="' + response.name + '">')
                uploadedFilesMap[file.name] = response.name
            },
            removedfile: function (file) {
                file.previewElement.remove()
                var name = ''
                if (typeof file.file_name !== 'undefined') {
                    name = file.file_name
                } else {
                    name = uploadedFilesMap[file.name]
                }
                $('form').find('input[name="files[]"][value="' + name + '"]').remove()
            },
            init: function () {
                @if(isset($lesson) && $lesson->files)

                var files =
                    {!! json_encode($lesson->files) !!}
                    for(
                var i
            in
                files
            )
                {
                    var file = files[i]
                    this.options.addedfile.call(this, file)
                    file.previewElement.classList.add('dz-complete')
                    $('form').append('<input type="hidden" name="files[]" value="' + file.file_name + '">')
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
