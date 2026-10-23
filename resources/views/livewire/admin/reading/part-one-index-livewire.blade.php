
<div class="table-responsive">
    <table class=" table table-bordered table-striped table-hover datatable datatable-Lesson mb-5">
        <thead>
        <tr>

            <th>
                Text

            </th>
            <th style="width: 10%">
                &nbsp;
            </th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>
                <textarea class="form-control" disabled  style="font-size: 18px" rows="12"> {{$text}} </textarea>
            </td>
            <td>
                <a class="btn btn-xs btn-info"
                   href="{{ route('admin.part1.text.edit', $readingExercise->part_one->id) }}">
                    {{ trans('global.edit') }}
                </a>
            </td>
        </tr>
        </tbody>
    </table>
    <table class=" table table-bordered table-striped table-hover datatable datatable-Lesson">
        <thead>
        <tr>
            <th width="10">
                N
            </th>

            <th >
                {{ trans('cruds.listening_part_one_test.fields.count_number') }}
            </th>
            <th>
                {{ trans('cruds.listening_part_one_test.fields.correct_answer') }}
            </th>
            <th>
                {{ trans('cruds.listening_part_one_test.fields.extra_answer') }}
            </th>
            <th style="width: 12%;">
                &nbsp;
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($readingExercise->part_one->answers as $key=>$answer)
            <tr>
                <td>
                    {{$key+1}}
                </td>
                <td>
                    [{{$answer->count_number}}]
                </td>
                <td>
                    {{$answer->correct_answer}}
                </td>
                <td>
                    {{$answer->extra_answer ?? ''}}
                </td>
                <td>

                    {{--            @can('lesson_edit')--}}
                    <a class="btn btn-xs btn-info"
                       href="{{ route('admin.part1.answer.edit', ['id'=>$answer->id,'reading_id'=>$readingExercise->id]) }}">
                        {{ trans('global.edit') }}
                    </a>
                    {{--            @endcan--}}

                    {{--            @can('lesson_delete')--}}
                    <form action="{{ route('admin.part1.answer.destroy', ['id'=>$answer->id,'reading_id'=>$readingExercise->id]) }} " method="POST"
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
    </table>
</div>
