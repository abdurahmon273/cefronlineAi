
<div class="table-responsive">
    <h3>Text</h3>
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
                <textarea class="form-control" disabled  style="font-size: 18px" rows="8"> {{$reading_exercise->part_four->text}} </textarea>
            </td>
            <td>
                <a class="btn btn-xs btn-info"
                   href="{{ route('admin.part4.text.edit', $reading_exercise->part_four->id) }}">
                    {{ trans('global.edit') }}
                </a>
            </td>
        </tr>
        </tbody>
    </table>
    <h3>True False Tests</h3>
    <table class=" table table-bordered table-striped table-hover datatable datatable-Lesson">
        <thead>
        <tr>
            <th width="10">
                N
            </th>
            {{--                        <th>--}}
            {{--                            {{ trans('cruds.reading_part_four.fields.id') }}--}}
            {{--                        </th>--}}
            <th style="width: 40%;">
                {{ trans('cruds.reading_part_four.fields.question') }}
            </th>
            <th style="width: 12%;">
                {{ trans('cruds.reading_part_four.fields.answer1') }}
            </th>
            <th style="width: 12%;">
                {{ trans('cruds.reading_part_four.fields.answer2') }}
            </th>
            <th style="width: 12%;">
                {{ trans('cruds.reading_part_four.fields.answer3') }}
            </th>
            <th style="width: 12%;">
                {{ trans('cruds.reading_part_four.fields.correct_answer') }}
            </th>
            <th style="width: 20%;">
                &nbsp;
            </th>
        </tr>
        </thead>
        <tbody >
        @php $count=0; @endphp
        @foreach($tfn_tests as $key => $test)
            @php $count+=1; @endphp

            <tr data-entry-id="{{ $test->id }}" wire:sortable.item="{{$test->id}}" >
                <td>
                    {{$count}}
                </td>

                <td>
                    <a style="color: black" href="">
                        {{ $test->question ?? '' }}
                    </a>
                </td>
                <td>
                    <a style="color: black" href="">
                        {{ $test->answer_1 ?? '' }}
                    </a>
                </td>

                <td>
                    <a style="color: black" href="">
                        {{ $test->answer_2 ?? '' }}
                    </a>
                </td>
                <td>
                    <a style="color: black" href="">
                        {{ $test->answer_3 ?? '' }}
                    </a>
                </td>
                <td>
                    <a style="color: black" href="">
                        {{ \App\Models\ListeningPartOneFifeTest::CORRECT_ANSWERS[ $test->correct_answer ] ?? '' }}
                    </a>
                </td>
                <td>

                    {{--            @can('lesson_edit')--}}
                    <a class="btn btn-xs btn-info"
                       href="{{ route('admin.part4.tfn.edit', $test->id) }}">
                        {{ trans('global.edit') }}
                    </a>
                    {{--            @endcan--}}

                    {{--            @can('lesson_delete')--}}
                    <form action="{{ route('admin.part4.tfn.destroy', $test->id) }} " method="POST"
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
    <h3>Multiple Choice</h3>
    <table class=" table table-bordered table-striped table-hover datatable datatable-Lesson mt-3">
        <thead>
        <tr>
            <th width="1">
                N
            </th>
            {{--                        <th>--}}
            {{--                            {{ trans('cruds.reading_part_four.fields.id') }}--}}
            {{--                        </th>--}}
            <th style="width: 30%;">
                {{ trans('cruds.reading_part_four.fields.question') }}
            </th>
            <th style="width: 14.5%;">
                {{ trans('cruds.reading_part_four.fields.answer1') }}
            </th>
            <th style="width: 14.5%;">
                {{ trans('cruds.reading_part_four.fields.answer2') }}
            </th>
            <th style="width: 14.5%;">
                {{ trans('cruds.reading_part_four.fields.answer3') }}
            </th>
            <th style="width: 14.5%;">
                {{ trans('cruds.reading_part_four.fields.answer4') }}
            </th>
            <th style="width: 15%;">
                &nbsp;
            </th>
        </tr>
        </thead>
        <tbody >
        @php $count=0; @endphp
        @foreach($mchq_tests as $key => $test)
            @php $count+=1; @endphp

            <tr data-entry-id="{{ $test->id }}" wire:sortable.item="{{$test->id}}" >
                <td>
                    {{$count}}
                </td>

                <td>
                    <a style="color: black" href="">
                        {{ $test->question ?? '' }}
                    </a>
                </td>
                <td >
                    <a style="color: black " href="">
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
                        {{ $test->answer_3 ?? '' }}  {{$test->correct_answer=='3' ? '✅' : ''}}
                    </a>
                </td>
                <td>
                    <a style="color: black" href="">
                        {{ $test->answer_4 ?? '' }}  {{$test->correct_answer=='4' ? '✅' : ''}}
                    </a>
                </td>
                <td>

                    {{--            @can('lesson_edit')--}}
                    <a class="btn btn-xs btn-info"
                       href="{{ route('admin.part4.mchq.edit', $test->id) }}">
                        {{ trans('global.edit') }}
                    </a>
                    {{--            @endcan--}}

                    {{--            @can('lesson_delete')--}}
                    <form action="{{ route('admin.part4.mchq.destroy', $test->id) }} " method="POST"
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
