
<div class="table-responsive">
    <table class=" table table-bordered table-striped table-hover datatable datatable-Lesson mb-3">
        <thead>
        <tr>
            <th width="10">
                N
            </th>

            <th style="width: 50%;">
                {{ trans('cruds.reading_part_three.fields.text') }}
            </th>


            <th>
                {{ trans('cruds.reading_part_three.fields.answer') }}
            </th>
            <th style="width: 12%">
                &nbsp;
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($readingExercise->part_three->texts as $key=>$text)
            <tr>
                <td>
                    {{$key+1}}
                </td>
                <td>
                    {{$text->text}}
                </td>
                <td>
                    {{$text->correct_answer->answer}}
                </td>
                <td>

                    {{--            @can('lesson_edit')--}}
                    <a class="btn btn-xs btn-info"
                       href="{{ route('admin.part3.text.edit', ['id'=>$text->id,'reading_id'=>$readingExercise->id]) }}">
                        {{ trans('global.edit') }}
                    </a>
                    {{--            @endcan--}}

                    {{--            @can('lesson_delete')--}}
                    <form action="{{ route('admin.part3.text.destroy', ['id'=>$text->id]) }} " method="POST"
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
    <table class=" table table-bordered table-striped table-hover datatable datatable-Lesson">
        <thead>
        <tr>
            <th width="10">
                N
            </th>

            <th >
                {{ trans('cruds.reading_part_three.fields.answer') }}
            </th>


            <th>
                {{ trans('cruds.reading_part_three.fields.letter') }}
            </th>
            <th style="width: 12%;">
                &nbsp;
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($readingExercise->part_three->answers as $key=>$answer)
            <tr>
                <td>
                    {{$key+1}}
                </td>
                <td>
                    {{$answer->answer}}
                </td>
                <td>
                    {{\App\Models\ReadingPartTwoThreeAnswer::LETTERS[$answer->letter]}}
                </td>
                <td>

                    {{--            @can('lesson_edit')--}}
                    <a class="btn btn-xs btn-info"
                       href="{{ route('admin.part3.heading.edit', ['id'=>$answer->id,'reading_id'=>$readingExercise->id]) }}">
                        {{ trans('global.edit') }}
                    </a>
                    {{--            @endcan--}}

                    {{--            @can('lesson_delete')--}}
                    <form action="{{ route('admin.part3.heading.destroy', ['id'=>$answer->id,'reading_id'=>$readingExercise->id]) }} " method="POST"
                          onsubmit="    return confirm('{{$answer->text ?  trans('global.cancel_delete_1') : trans('global.areYouSure')  }}');"
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
