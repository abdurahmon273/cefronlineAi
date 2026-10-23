@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.news.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.newss.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.news.fields.id') }}
                        </th>
                        <td>
                            {{ $news->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.news.fields.title') }}
                        </th>
                        <td>
                            {{ $news->title }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.news.fields.description') }}
                        </th>
                        <td>
                            {!! $news->description !!}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.video.fields.link') }}
                        </th>
                        <td>
                             {{$news->link}}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.news.fields.status') }}
                        </th>
                        <td>
                            {{ App\Models\News::STATUS_SELECT[$news->status] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.news.fields.application_button') }}
                        </th>
                        <td>
                            {{ App\Models\News::APPLICATION_BUTTON_SELECT[$news->application_button] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.news.fields.image') }}
                        </th>
                        <td>
                            @if($news->image)
                                <a href="{{ $news->image->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img style="width: 100%; height: 300px" src="{{ $news->image->getUrl() }}">
                                </a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.news.fields.web_image') }}
                        </th>
                        <td>
                            @if($news->web_image)
                                <a href="{{ $news->web_image->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img style="width: 100%; height: 300px" src="{{ $news->web_image->getUrl() }}">
                                </a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.news.fields.video_link') }}
                        </th>
                        <td>
                            @if($news->status==1)
                                @if(!empty($news->video_link))
                                    <iframe width="100%" height="400" src="https://www.youtube.com/embed/{{ $news->video_link }}" frameborder="0" allowfullscreen></iframe>
                                @else
                                    <iframe width="100%" height="400" src="https://www.youtube.com/embed/{{ $news->video_link }}" frameborder="0" allowfullscreen></iframe>
                                @endif
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.newss.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection
