<div>
    <tbody>
    @foreach($writingExercises as $key => $exercise)
        <tr data-entry-id="{{ $exercise->id }}" wire:sortable.item="{{$exercise->id}}" >
            <td>

            </td>
            <td  wire:sortable.handle>
                {{ $exercise->id ?? '' }}
            </td>
            <td>
                <a style="color: black"
{{--                   {{ route(\App\Models\ReadingExercise::PART_LINKS[$exercise->part_category],['id'=>$exercise->id]) }}--}}
                   href="">
                    {{ $exercise->name ?? '' }}
                </a>
            </td>

            <td>
                {{ \App\Models\WritingExercise::PART_CATEGORIES[$exercise->part_category] ?? '' }}
            </td>
            <td>
                {{ \App\Models\Lesson::STATUS[$exercise->status] ?? 'free' }}
            </td>
            <td>

                {{--            @can('lesson_edit')--}}
                <a class="btn btn-xs btn-info"
                   href="{{ route('admin.writings.exercise.edit', $exercise->id) }}">
                    {{ trans('global.edit') }}
                </a>
                {{--            @endcan--}}

                {{--            @can('lesson_delete')--}}
                <form action="{{ route('admin.writings.exercise.destroy', $exercise->id) }} " method="POST"
                      onsubmit="    return confirm('{{ trans('global.readingYouSure') }}');"
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
</div>
