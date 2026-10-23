<tbody wire:sortable="updateOrder">
@foreach($videos as $key => $video)
    <tr data-entry-id="{{ $video->id }}" wire:sortable.item="{{$video->id}}">
        <td>

        </td>
        <td>
            {{ $video->id ?? '' }}
        </td>
        <td>
            {{ $video->name ?? '' }}
        </td>
        <td>
            {{ $video->link ?? '' }}
        </td>
        <td>
            {{ $video->lesson->name ?? '' }}
        </td>
        <td>
            {{ \App\Models\Video::STATUS[$video->status] ?? '' }}
        </td>
        <td>
            @foreach($video->files as $key => $media)
                <a href="{{ $media->getUrl() }}" target="_blank">
                    {{ trans('global.view_file') }}
                </a>
            @endforeach
        </td>
        <td>
            @can('video_show')
                <a class="btn btn-xs btn-primary" href="{{ route('admin.videos.show', $video->id) }}">
                    {{ trans('global.view') }}
                </a>
            @endcan

            @can('video_edit')
                <a class="btn btn-xs btn-info" href="{{ route('admin.videos.edit', $video->id) }}">
                    {{ trans('global.edit') }}
                </a>
            @endcan

            @can('video_delete')
                <form action="{{ route('admin.videos.destroy', $video->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                </form>
            @endcan

        </td>

    </tr>
@endforeach
</tbody>
