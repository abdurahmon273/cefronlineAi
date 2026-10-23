@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.news.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.newss.update', [$news->id]) }}" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="form-group">
                    <label class="required" for="title">{{ trans('cruds.news.fields.title') }}</label>
                    <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" type="text" name="title"
                        id="title" value="{{ old('title', $news->title) }}" required>
                    @if ($errors->has('title'))
                        <div class="invalid-feedback">
                            {{ $errors->first('title') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.news.fields.title_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="description">{{ trans('cruds.news.fields.description') }}</label>
                    <textarea class="form-control ckeditor {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description"
                        id="description">{!! old('description', $news->description) !!}</textarea>
                    @if ($errors->has('description'))
                        <div class="invalid-feedback">
                            {{ $errors->first('description') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.news.fields.description_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="link">{{ trans('cruds.video.fields.link') }}</label>
                    <input class="form-control {{ $errors->has('link') ? 'is-invalid' : '' }}" type="text"
                        name="link" id="link" value="{{ old('link', $news->link) }}">
                    @if ($errors->has('link'))
                        <div class="invalid-feedback">
                            {{ $errors->first('link') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.news.fields.video_link_helper') }}</span>
                </div>
                @livewire('admin.newss.edit-news-livewire', ['news' => $news])
                <input type="text" name="status" hidden="" class="form-control" id="inp2" value="">

                <div class="form-group d-none" id="img2">
                    <label for="image">{{ trans('cruds.news.fields.image') }}</label>
                    <div class="needsclick dropzone {{ $errors->has('image') ? 'is-invalid' : '' }}" id="image-dropzone">
                    </div>
                    @if ($errors->has('image'))
                        <div class="invalid-feedback">
                            {{ $errors->first('image') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.news.fields.image_helper') }}</span>
                </div>

                <div class="form-group d-none" id="web_img">
                    <label for="web_image">{{ trans('cruds.news.fields.web_image') }}</label>
                    <div class="needsclick dropzone {{ $errors->has('web_image') ? 'is-invalid' : '' }}"
                        id="web-image-dropzone">
                    </div>
                    @if ($errors->has('web_image'))
                        <div class="invalid-feedback">
                            {{ $errors->first('web_image') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.news.fields.web_image_helper') }}</span>
                </div>

                <div class="form-group d-none" id="vid2">
                    <label for="video_link">{{ trans('cruds.news.fields.video_link') }}</label>
                    <input class="form-control {{ $errors->has('video_link') ? 'is-invalid' : '' }}" type="text"
                        name="video_link" id="video_link" value="{{ old('video_link', $news->video_link) }}">
                    @if ($errors->has('video_link'))
                        <div class="invalid-feedback">
                            {{ $errors->first('video_link') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.news.fields.video_link_helper') }}</span>
                </div>
                <div class="form-group">
                    <label>{{ trans('cruds.news.fields.application_button') }}</label>
                    <select class="form-control {{ $errors->has('application_button') ? 'is-invalid' : '' }}"
                        name="application_button" id="application_button">
                        <option value disabled {{ old('application_button', null) === null ? 'selected' : '' }}>
                            {{ trans('global.pleaseSelect') }}</option>
                        @foreach (App\Models\News::APPLICATION_BUTTON_SELECT as $key => $label)
                            <option value="{{ $key }}"
                                {{ old('application_button', $news->application_button) === (string) $key ? 'selected' : '' }}>
                                {{ $label }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('application_button'))
                        <div class="invalid-feedback">
                            {{ $errors->first('application_button') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.news.fields.application_button_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="link">{{ trans('cruds.news.fields.button_text') }}</label>
                    <input class="form-control {{ $errors->has('button_text') ? 'is-invalid' : '' }}" type="text"
                        name="button_text" id="button_text" value="{{ old('button_text', $news->button_text) }}">
                    @if ($errors->has('button_text'))
                        <div class="invalid-feedback">
                            {{ $errors->first('button_text') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.news.fields.video_link_helper') }}</span>
                </div>

                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.newss.index') }}">
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
        document.addEventListener('status_updated', function(status_updated) {

            let img2 = document.getElementById('img2');
            let web_img = document.getElementById('web_img');
            let vid2 = document.getElementById('vid2');
            let inp2 = document.getElementById('inp2');
            let video_link = document.getElementById('video_link');
            let status_type = status_updated['detail'][0]['status_type']

            if (status_type == false) {
                vid2.classList.add('d-none');
                img2.classList.remove('d-none')
                web_img.classList.remove('d-none')
                inp2.value = '0'
                video_link.value = ''
            }

            if (status_type == true) {
                img2.classList.add('d-none');
                web_img.classList.add('d-none')
                vid2.classList.remove('d-none')
                inp2.value = '1'

            }
            if (status_type == null) {
                img2.classList.add('d-none');
                web_img.classList.add('d-none')
                vid2.classList.add('d-none')
                inp2.value = ''
                video_link.value = ''
            }
        })
    </script>
    <script>
        $(document).ready(function() {
            function SimpleUploadAdapter(editor) {
                editor.plugins.get('FileRepository').createUploadAdapter = function(loader) {
                    return {
                        upload: function() {
                            return loader.file
                                .then(function(file) {
                                    return new Promise(function(resolve, reject) {
                                        // Init request
                                        var xhr = new XMLHttpRequest();
                                        xhr.open('POST',
                                            '{{ route('admin.newss.storeCKEditorImages') }}',
                                            true);
                                        xhr.setRequestHeader('x-csrf-token', window._token);
                                        xhr.setRequestHeader('Accept', 'application/json');
                                        xhr.responseType = 'json';

                                        // Init listeners
                                        var genericErrorText =
                                            `Couldn't upload file: ${ file.name }.`;
                                        xhr.addEventListener('error', function() {
                                            reject(genericErrorText)
                                        });
                                        xhr.addEventListener('abort', function() {
                                            reject()
                                        });
                                        xhr.addEventListener('load', function() {
                                            var response = xhr.response;

                                            if (!response || xhr.status !== 201) {
                                                return reject(response && response
                                                    .message ?
                                                    `${genericErrorText}\n${xhr.status} ${response.message}` :
                                                    `${genericErrorText}\n ${xhr.status} ${xhr.statusText}`
                                                );
                                            }

                                            $('form').append(
                                                '<input type="hidden" name="ck-media[]" value="' +
                                                response.id + '">');

                                            resolve({
                                                default: response.url
                                            });
                                        });

                                        if (xhr.upload) {
                                            xhr.upload.addEventListener('progress', function(
                                                e) {
                                                if (e.lengthComputable) {
                                                    loader.uploadTotal = e.total;
                                                    loader.uploaded = e.loaded;
                                                }
                                            });
                                        }

                                        // Send request
                                        var data = new FormData();
                                        data.append('upload', file);
                                        data.append('crud_id', '{{ $news->id ?? 0 }}');
                                        xhr.send(data);
                                    });
                                })
                        }
                    };
                }
            }

            var allEditors = document.querySelectorAll('.ckeditor');
            for (var i = 0; i < allEditors.length; ++i) {
                ClassicEditor.create(
                    allEditors[i], {
                        extraPlugins: [SimpleUploadAdapter]
                    }
                );
            }
        });
    </script>

    <script>
        Dropzone.options.imageDropzone = {
            url: '{{ route('admin.newss.storeMedia') }}',
            maxFilesize: 5, // MB
            acceptedFiles: '.jpeg,.jpg,.png,.gif',
            maxFiles: 1,
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            params: {
                size: 5,
                width: 4096,
                height: 4096
            },
            success: function(file, response) {
                $('form').find('input[name="image"]').remove()
                $('form').append('<input type="hidden" name="image" value="' + response.name + '">')
            },
            removedfile: function(file) {
                file.previewElement.remove()
                if (file.status !== 'error') {
                    $('form').find('input[name="image"]').remove()
                    this.options.maxFiles = this.options.maxFiles + 1
                }
            },
            init: function() {
                @if (isset($news) && $news->image)
                    var file = {!! json_encode($news->image) !!}
                    this.options.addedfile.call(this, file)
                    this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
                    file.previewElement.classList.add('dz-complete')
                    $('form').append('<input type="hidden" name="image" value="' + file.file_name + '">')
                    this.options.maxFiles = this.options.maxFiles - 1
                @endif
            },
            error: function(file, response) {
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
        Dropzone.options.webImageDropzone = {
            url: '{{ route('admin.newss.storeMedia') }}',
            maxFilesize: 5, // MB
            acceptedFiles: '.jpeg,.jpg,.png,.gif',
            maxFiles: 1,
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            params: {
                size: 5,
                width: 4096,
                height: 4096
            },
            success: function(file, response) {
                $('form').find('input[name="web_image"]').remove()
                $('form').append('<input type="hidden" name="web_image" value="' + response.name + '">')
            },
            removedfile: function(file) {
                file.previewElement.remove()
                if (file.status !== 'error') {
                    $('form').find('input[name="web_image"]').remove()
                    this.options.maxFiles = this.options.maxFiles + 1
                }
            },
            init: function() {
                @if (isset($news) && $news->web_image)
                    var file = {!! json_encode($news->web_image) !!}
                    this.options.addedfile.call(this, file)
                    this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
                    file.previewElement.classList.add('dz-complete')
                    $('form').append('<input type="hidden" name="web_image" value="' + file.file_name + '">')
                    this.options.maxFiles = this.options.maxFiles - 1
                @endif
            },
            error: function(file, response) {
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
