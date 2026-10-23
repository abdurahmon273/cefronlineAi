<tbody wire:sortable="updateOrder">
@foreach($lessons as $key => $lesson)
    <tr data-entry-id="{{ $lesson->id }}" wire:sortable.item="{{$lesson->id}}" >
        <td>

        </td>
        <td  wire:sortable.handle>
            {{ $lesson->id ?? '' }}
        </td>
        <td>
            <a style="color: black"
               href="{{ route('admin.videos.index2',['id'=>$lesson->id]) }}">
                {{ $lesson->name ?? '' }}
            </a>
        </td>
        <td>
            {{ $lesson->course->name ?? '' }}
        </td>
        <td>
            @foreach($lesson->files as $key => $media)
                <a href="{{ $media->getUrl() }}" target="_blank">
                    {{ trans('global.view_file') }}
                </a>
            @endforeach
        </td>
        <td>
            {{ \App\Models\Lesson::STATUS[$lesson->status] ?? '' }}
        </td>
        <td>
            @can('lesson_show')
                <a class="btn btn-xs btn-primary"
                   href="{{ route('admin.lessons.show', $lesson->id) }}">
                    {{ trans('global.view') }}
                </a>
            @endcan

            @can('lesson_edit')
                <a class="btn btn-xs btn-info"
                   href="{{ route('admin.lessons.edit', $lesson->id) }}">
                    {{ trans('global.edit') }}
                </a>
            @endcan

            @can('lesson_delete')
                <form action="{{ route('admin.lessons.destroy', $lesson->id) }}" method="POST"
                      onsubmit="    return confirm('{{ trans('global.areYouSure') }}');"
                      style="display: inline-block;">
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="submit" class="btn btn-xs btn-danger"
                           value="{{ trans('global.delete') }}">
                </form>
            @endcan

        </td>

    </tr>
@endforeach
</tbody>
