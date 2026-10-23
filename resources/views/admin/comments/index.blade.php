@extends('layouts.admin')
@section('content')
@can('comment_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
{{--            <a class="btn btn-success" href="{{ route('admin.comments.create') }}">--}}
{{--                {{ trans('global.add') }} {{ trans('cruds.comment.title_singular') }}--}}
{{--            </a>--}}
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.comment.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Comment">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.comment.fields.id') }}
                        </th>

                        <th>
                            {{ trans('cruds.comment.fields.user') }}
                        </th>
                        <th>
                            {{ trans('cruds.comment.fields.comment') }}
                        </th>
                        <th>
                            {{ trans('cruds.comment.fields.video') }}
                        </th>
                        <th>
                            {{ trans('cruds.comment.fields.date') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($comments as $key => $comment)
                        <tr data-entry-id="{{ $comment->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $comment->id ?? '' }}
                            </td>
                            <td>
                                {{ $comment->user->name ?? '' }} {{$comment->user->surname ?? ''}}
                            </td>
                            <td>
                                {{ $comment->comment ?? '' }}
                            </td>
                            <td>
                                <a href="{{route('admin.videos.index2',$comment->video->lesson_id)}}">
                                    {{ $comment->video->name ?? '' }}
                                </a>
                            </td>

                            <td>
                                {{ date_format($comment->created_at,'d.m.Y') ?? '' }}
                            </td>
                            <td>
                                @can('comment_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.comments.show', $comment->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan


                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $comments->links() }}
        </div>
    </div>
</div>



@endsection
@section('scripts')
@parent
@endsection
