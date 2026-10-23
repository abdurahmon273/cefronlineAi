    <tbody wire:sortable="updateOrder">
    @foreach($speakingExercises as $key => $exercise)
        <tr data-entry-id="{{ $exercise->id }}" wire:sortable.item="{{$exercise->id}}" >
            <td>

            </td>
            <td  wire:sortable.handle>
                {{ $exercise->id ?? '' }}
            </td>

            <td>
                <a style="color: black"
                   {{--                   {{ route(\App\Models\ReadingExercise::PART_LINKS[$exercise->part_category],['id'=>$exercise->id]) }}--}}
                   href="#">
                    {{ $exercise->title ?? '' }}
                </a>
            </td>
            <td>
                    <a class="btn btn-xs {{(count($exercise->part_one_first->questions)>=1 && count($exercise->part_one_second->questions)>=1 && count($exercise->part_one_second->images)>=1)? 'btn-info': 'btn-warning' }}"
                       href="{{ route('admin.speaking.part1.index', $exercise->id) }}">
                        {{ trans('cruds.speaking.fields.title_part_one') }}
                    </a>

            </td>
            <td>

                <a class="btn btn-xs {{(count($exercise->part_two->questions)>=1 && count($exercise->part_two->images)>=1 )? 'btn-info': 'btn-warning' }} "
                   href="{{ route('admin.speaking.part2.index', $exercise->id) }}">
                    {{ trans('cruds.speaking.fields.title_part_two') }}
                </a>
            </td>
            <td>
                <a class="btn btn-xs {{(strlen($exercise->part_three->question_text)>='5')? 'btn-info': 'btn-warning' }} "
                   href="{{ route('admin.speaking.part3.index', $exercise->id) }}">
                    {{ trans('cruds.speaking.fields.title_part_three') }}
                </a>
            </td>


            <td>
                {{ $exercise->part_three->time }} seconds
            </td>

            <td>
                {{ \App\Models\Lesson::STATUS[$exercise->status] ?? 'free' }}
            </td>
            <td>

                {{--            @can('lesson_edit')--}}
                <a class="btn btn-xs btn-info"
                   href="{{ route('admin.speakings_exercise.full.edit', $exercise->id) }}">
                    {{ trans('global.edit') }}
                </a>
                {{--            @endcan--}}

                {{--            @can('lesson_delete')--}}
                <form action="{{ route('admin.speaking.exercises.destroy', $exercise->id) }} " method="POST"
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
    <div class="mt-4">
        {{ $speakingExercises->links() }}
    </div>
    </tbody>
