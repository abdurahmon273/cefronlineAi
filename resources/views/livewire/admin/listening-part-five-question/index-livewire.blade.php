<tbody >
@php $count=0; @endphp
@foreach($tests as $key => $test)
    @php $count+=1; @endphp

    <tr data-entry-id="{{ $test->id }}" wire:sortable.item="{{$test->id}}" >
        <td>
            {{$count}}
        </td>

        <td>
            <a style="color: black" href="">
                {{ $test->question}}
            </a>
        </td>
        <td>
            <a style="color: black" href="">
                {{ $test->answer_1 ?? '' }}  {{$test->correct_answer=='1' ? '✅' : ''}}
            </a>
        </td>

        <td>
            <a style="color: black" href="">
                {{ $test->answer_2 ?? '' }}  {{$test->correct_answer=='2' ? '✅' : ''}}
            </a>
        </td>
        <td>
            <a style="color: black" href="">
                {{ $test->answer_3 ?? '' }} {{$test->correct_answer=='3' ? '✅' : ''}}
            </a>
        </td>
{{--        <td>--}}
{{--            <a style="color: black" href="">--}}
{{--                {{ \App\Models\ListeningPartOneFifeTest::CORRECT_ANSWERS[ $test->correct_answer ] ?? '' }}--}}
{{--            </a>--}}
{{--        </td>--}}
        <td>

            {{--            @can('lesson_edit')--}}
            <a class="btn btn-xs btn-info"
               href="{{ route('admin.listenings.part5.edit', $test->id) }}">
                {{ trans('global.edit') }}
            </a>
            {{--            @endcan--}}

            {{--            @can('lesson_delete')--}}
            <form action="{{ route('admin.listenings.part1.destroy', $test->id) }} " method="POST"
                  onsubmit="    return confirm('{{ trans('global.areYouSure') }}');"
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
