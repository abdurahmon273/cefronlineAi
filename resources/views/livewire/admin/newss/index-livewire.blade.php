<tbody wire:sortable="updateOrder">
@foreach($newss as $key => $news)
    <tr data-entry-id="{{ $news->id }}" wire:sortable.item="{{ $news->id }}">
        <td></td>

        <td wire:sortable.handle>
            {{ $news->id ?? '' }}
        </td>

        <td>
            <a style="color: black" href="{{ route('admin.newsJoin', ['id' => $news->id]) }}">
                {{$news->title }}
            </a>
        </td>

        <td>
            @if($news->status == 0)
                <img style="width:100%; height:200px; border-top-right-radius: 8px; border-top-left-radius: 8px;"
                     src="{{ $news->image ? $news->image->getUrl() : '' }}">
            @endif
        </td>

        <td>
            @if($news->status == 0)
                <img style="width:100%; height:200px; border-top-right-radius: 8px; border-top-left-radius: 8px;"
                     src="{{ $news->web_image ? $news->web_image->getUrl() : '' }}">
            @endif
        </td>

        <td>
            <a class="btn btn-xs btn-info" href="{{ route('admin.newss.edit', [$news->id]) }}">
                {{ trans('global.edit') }}
            </a>

            <a class="btn btn-xs btn-info" href="{{ route('admin.newss.show', [$news->id]) }}">
                {{ trans('global.show') }}
            </a>

            <form action="{{ route('admin.newss.destroy', $news->id) }}" method="POST"
                  onsubmit="return confirm('{{ trans('global.listeningYouSure') }}');"
                  style="display: inline-block;">
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
            </form>
        </td>
    </tr>
@endforeach
</tbody>
