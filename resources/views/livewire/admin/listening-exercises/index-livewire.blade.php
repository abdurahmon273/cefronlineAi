<tbody wire:sortable="updateOrder">
{{--@dd($listeningExercises)--}}
@foreach($listeningExercises as $key => $listening)
    <tr data-entry-id="{{ $listening->id }}" wire:sortable.item="{{$listening->id}}" >
        <td>

        </td>
        <td  wire:sortable.handle>
            {{ $listening->id ?? '' }}
        </td>
        <td>
            <a style="color: black"
               href="{{ route( \App\Models\ListeningExercise::PART_LINKS[$listening->part_category] ,['id'=>$listening->id,'exercise_id'=>$exercise_id]) }}">
                {{ $listening->name ?? '' }}
            </a>
        </td>

        <td>
           {{ \App\Models\ListeningExercise::PART_CATEGORIES[$listening->part_category] }}
        </td>
        <td>
            {{ \App\Models\ListeningExercise::STATUS[$listening->status] ?? 'free' }}
        </td>
        <td>

            {{--            @can('lesson_edit')--}}
            <a class="btn btn-xs btn-info"
               href="{{ route('admin.listening.edit', $listening->id) }}">
                {{ trans('global.edit') }}
            </a>
            {{--            @endcan--}}

            {{--            @can('lesson_delete')--}}
            <form action="{{ route('admin.listening.destroy', $listening->id) }} " method="POST"
                  onsubmit="    return confirm('{{ trans('global.listeningYouSure') }}');"
                  style="display: inline-block;">
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="submit" class="btn btn-xs btn-danger"
                       value="{{ trans('global.delete') }}">
            </form>
            {{--            @endcan--}}

        </td>

    </tr>
@endforeach
</tbody>
