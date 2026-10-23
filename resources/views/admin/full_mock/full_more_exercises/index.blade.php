@extends('layouts.admin')

@section('content')
    @can('course_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-default" href="{{ route('admin.full_exercise.index',$full_exercise->exercise_id) }}">
                    {{ trans('global.back_to_list') }}
                </a>

            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            <p style="font-size: 30px">{{ trans('cruds.exercise.full_exercise_index') }} </p>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-Lesson">
                    <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th style="width: 5%">
                            {{ trans('cruds.lesson.fields.id') }}
                        </th>

                        <th>
                            {{ trans('cruds.exercise.fields.category_type') }}
                        </th>
                        <th>
                            {{ trans('cruds.exercise.fields.part_status') }}
                        </th>
                        <th style="width: 10%">
                            &nbsp;
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>

                            </td>
                            <td >
                                {{ $full_exercise->listening_exercise->id ?? '' }}
                            </td>
                            <td>
                                @if($full_exercise->listening_exercise->part_one)
                                    <a style="color: black"
                                       href="{{route('admin.listening_full_exercise.index',$full_exercise->listening_exercise->id)}}">
                                        {{trans('cruds.listening.title')}}
                                    </a>
                                @else
                                    <a style="color: black"
                                       href="">
                                        {{trans('cruds.listening.title')}}
                                    </a>
                                @endif

                            </td>



                            <td>
                                @if($full_exercise->listening_exercise->part_one && count($full_exercise->listening_exercise->part_one->tests)>=7 && count($full_exercise->listening_exercise->part_five->tests)>=6 && count($full_exercise->listening_exercise->part_two->listening_part_two_six_tests)>=6 &&  count($full_exercise->listening_exercise->part_six->listening_part_two_six_tests)>=6 && count($full_exercise->listening_exercise->part_three->part_three_four_questions)>=4&& count($full_exercise->listening_exercise->part_four->part_three_four_questions)>=5  )
                                    <a class="btn btn-xs text-white btn-info">
                                        {{ trans('cruds.exercise.fields.completed') }}
                                    </a>
                                @elseif($full_exercise->listening_exercise->part_one)
                                    <a class="btn btn-xs text-white btn-warning">
                                        {{ trans('cruds.exercise.fields.process') }}
                                    </a>
                                @else
                                    <a class="btn btn-xs text-white btn-danger">
                                        {{ trans('cruds.exercise.fields.creating_parts') }}
                                    </a>
                                @endif


                            </td>
                            <td>

                                {{--            @can('lesson_edit')--}}
                                <a class="btn btn-xs btn-info"
                                   href="{{ route('admin.full.listening_exercise.change', $full_exercise->id) }}">
                                    {{ trans('global.change') }}
                                </a>
                                {{--            @endcan--}}

                            </td>

                        </tr>
                        <tr>
                            <td>
                            </td>
                            <td >
                                {{ $full_exercise->reading_exercise->id ?? '' }}
                            </td>
                            <td>
                                @if($full_exercise->reading_exercise->part_one)
                                    <a style="color: black"
                                       href="{{route('admin.reading_full_exercise.index',$full_exercise->reading_exercise->id)}}">
                                        {{trans('cruds.reading.title')}}
                                    </a>
                                @else
                                    <a style="color: black"
                                       href="">
                                        {{trans('cruds.reading.title')}}
                                    </a>
                                @endif

                            </td>


                            <td>
                                @if( $full_exercise->reading_exercise->part_one &&
                                    count($full_exercise->reading_exercise->part_one->answers->toArray())>=6 &&
                                    count($full_exercise->reading_exercise->part_five->tests->toArray())>=2 &&
                                    count($full_exercise->reading_exercise->part_five->answers->toArray())>=4 &&
                                    count($full_exercise->reading_exercise->part_two->texts->toArray())>=7 &&
                                    count($full_exercise->reading_exercise->part_three->texts->toArray())>=6 &&
                                    count($full_exercise->reading_exercise->part_four->tests->toArray())>=9  )
                                    <a class="btn btn-xs text-white btn-info">
                                        {{ trans('cruds.exercise.fields.completed') }}
                                    </a>
                                @elseif($full_exercise->reading_exercise->part_one)
                                    <a class="btn btn-xs text-white btn-warning">
                                        {{ trans('cruds.exercise.fields.process') }}
                                    </a>

                                @else
                                    <a class="btn btn-xs text-white btn-danger">
                                        {{ trans('cruds.exercise.fields.creating_parts') }}
                                    </a>
                                @endif

                            </td>
                            <td>

                                <a class="btn btn-xs btn-info"
                                   href="{{ route('admin.full.reading_exercise.change', $full_exercise->id) }}">
                                    {{ trans('global.change') }}
                                </a>


                            </td>

                        </tr>
                        <tr>
                            <td>

                            </td>
                            <td >
                                {{ $full_exercise->writing_exercise->id ?? '' }}
                            </td>
                            <td>
                                <a style="color: black"
                                   href="">
                                    {{trans('cruds.writing.title')}}
                                </a>
                            </td>



                            <td>
                                @if($full_exercise->writing_exercise->task_one && $full_exercise->writing_exercise->task_two)
                                    <a class="btn btn-xs text-white btn-info">
                                        {{ trans('cruds.exercise.fields.completed') }}
                                    </a>
                                @elseif($full_exercise->writing_exercise->task_one || $full_exercise->writing_exercise->task_two)
                                    <a class="btn btn-xs text-white btn-warning">
                                        {{ trans('cruds.exercise.fields.process') }}
                                    </a>
                                @else
                                    <a class="btn btn-xs text-white btn-danger">
                                        {{ trans('cruds.exercise.fields.creating_parts') }}
                                    </a>
                                @endif

                            </td>
                            <td>
                                <a class="btn btn-xs btn-info"
                                   href="{{ route('admin.full.writing_exercise.change', $full_exercise->id) }}">
                                    {{ trans('global.change') }}
                                </a>
                            </td>

                        </tr>
                        <tr>
                            <td>

                            </td>
                            <td >
                                {{ $full_exercise->speaking_exercise->id ?? '' }}
                            </td>
                            <td>
                                @if($full_exercise->speaking_exercise->part_one_first)
                                    <a style="color: black"
                                       href="{{route('admin.speaking_full_exercise.index',$full_exercise->speaking_exercise->id)}}">
                                        {{trans('cruds.speaking.title')}}
                                    </a>
                                @else
                                    <a style="color: black"
                                       href="">
                                        {{trans('cruds.speaking.title')}}
                                    </a>
                                @endif

                            </td>



                            <td>
                                @if($full_exercise->speaking_exercise->part_one_first &&
                                    count($full_exercise->speaking_exercise->part_one_first->questions->toArray()) >=1 &&
                                    count($full_exercise->speaking_exercise->part_one_second->questions->toArray()) >=1  &&
                                    count($full_exercise->speaking_exercise->part_one_second->images->toArray())>=1 &&
                                    count($full_exercise->speaking_exercise->part_two->questions->toArray()) >=1 &&
                                    count($full_exercise->speaking_exercise->part_two->images->toArray()) >=1 &&
                                    $full_exercise->speaking_exercise->part_three->question_text )
                                    <a class="btn btn-xs text-white btn-info">
                                        {{ trans('cruds.exercise.fields.completed') }}
                                    </a>
                                @elseif($full_exercise->speaking_exercise->part_one_first)
                                    <a class="btn btn-xs text-white btn-warning">
                                        {{ trans('cruds.exercise.fields.process') }}
                                    </a>
                                @else
                                    <a class="btn btn-xs text-white btn-danger">
                                        {{ trans('cruds.exercise.fields.creating_parts') }}
                                    </a>
                                @endif


{{--                                <a class="btn btn-xs text-white btn-success">--}}
{{--                                    {{ trans('cruds.exercise.fields.completed') }}--}}
{{--                                </a>--}}

                            </td>
                            <td>

                                {{--            @can('lesson_edit')--}}
                                <a class="btn btn-xs btn-info"
                                   href="{{ route('admin.full.speaking_exercise.change', $full_exercise->id)  }}">
                                    {{ trans('global.change') }}
                                </a>
                                {{--            @endcan--}}


                            </td>

                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
