@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.video.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.videos.index2',['id'=>$video->lesson->id]) }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.video.fields.id') }}
                        </th>
                        <td>
                            {{ $video->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.video.fields.link') }}
                        </th>
                        <td>
                            {{ $video->link }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.video.fields.lesson') }}
                        </th>
                        <td>
                            {{ $video->lesson->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.video.fields.description') }}
                        </th>
                        <td>
                            {{ $video->description }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.video.fields.status') }}
                        </th>
                        <td>
                            {{ \App\Models\Video::STATUS[$video->status] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.video.fields.files') }}
                        </th>
                        <td>
                            @foreach($video->files as $key => $media)
                                <a href="{{ $media->getUrl() }}" target="_blank">
                                    {{ trans('global.view_file') }}
                                </a>
                            @endforeach
                        </td>
{{--                        @dd()--}}
                    </tr>
                </tbody>
            </table>
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                    <th>id</th>
                    <th>comment</th>
                    <th>user</th>
                </tr>
              </thead>
                <tbody>
                @foreach($comments as $comment)
                   <tr>
                       <td>{{$comment['id']}}</td>
                       <td>{{$comment['comment']}}</td>
                       <td>{{$comment['name']}}</td>
                   </tr>
                @endforeach

                </tbody>
            </table>
            {{ $comments->links() }}
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.videos.index2',['id'=>$video->lesson->id]) }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection
