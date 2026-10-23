@extends('layouts.admin')

@section('styles')
    <style>
        /* Style for all <a> elements */
        a {
            color: black; /* Black text color */
            text-decoration: none; /* No underline */
            font-weight: normal; /* Regular font weight */
        }

        a:hover{
            color: black; /* Black text color */
            text-decoration: none; /* No underline */
            font-weight: normal; /* Regular font weight */
        }
       .process{
           position: absolute;
           z-index: 100;
       }

    </style>
@endsection
@section('content')
    @can('course_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-default" href="{{ route('admin.full_exercise.more.index',$reading_exercise->full_exercise->id) }}">
                    {{ trans('global.back_to_list') }}
                </a>

            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            <p style="font-size: 30px">{{ trans('cruds.reading.title_parts') }} </p>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <div>

                    <div class="row ">
                        <div class="col-6 "  draggable="true">
                            @if(count($reading_exercise->part_one->answers->toArray())>=6)
                                <a class="btn btn-xs process text-white btn-info">
                                    {{ trans('cruds.exercise.fields.completed') }}
                                </a>
                            @elseif(count($reading_exercise->part_one->answers->toArray())>=1)
                                <a  class="btn btn-xs process btn-warning">
                                    {{ trans('cruds.exercise.fields.process') }}
                                </a>
                            @else
                                <a class="btn btn-xs process text-white btn-danger">
                                    {{ trans('cruds.exercise.fields.creating') }}
                                </a>
                            @endif

                            <a href="{{route('admin.reading.part1.index',['id'=>$reading_exercise->id,'exercise_id'=>$reading_exercise->exercise_id])}}" class="card c-header border-none w-100 pt-5 pb-5 d-flex justify-content-center align-items-center"  style="border-radius: 8px;">
                                <h4 class="card-title">
                                    Part One
                                </h4>
                            </a>
                        </div>
                        <div class="col-6 "  draggable="true">
                            @if(count($reading_exercise->part_two->texts->toArray())>=6)
                                <a class="btn btn-xs process text-white btn-info">
                                    {{ trans('cruds.exercise.fields.completed') }}
                                </a>
                            @elseif(count($reading_exercise->part_two->texts->toArray())>=1)
                                <a  class="btn btn-xs process btn-warning">
                                    {{ trans('cruds.exercise.fields.process') }}
                                </a>
                            @else
                                <a class="btn btn-xs process text-white btn-danger">
                                    {{ trans('cruds.exercise.fields.creating') }}
                                </a>
                            @endif
                            <a href="{{route('admin.reading.part2.index',['id'=>$reading_exercise->id,'exercise_id'=>$reading_exercise->exercise_id])}}" class="card c-header border-none w-100 pt-5 pb-5 d-flex justify-content-center align-items-center"  style="border-radius: 8px;">
                                <h4 class="card-title">
                                    Part Two
                                </h4>
                            </a>
                        </div>
                        <div class="col-6 " draggable="true">
                            @if(count($reading_exercise->part_three->texts->toArray())>=6)
                                <a class="btn btn-xs process text-white btn-info">
                                    {{ trans('cruds.exercise.fields.completed') }}
                                </a>
                            @elseif(count($reading_exercise->part_three->texts->toArray())>=1)
                                <a  class="btn btn-xs process btn-warning">
                                    {{ trans('cruds.exercise.fields.process') }}
                                </a>
                            @else
                                <a class="btn btn-xs process text-white btn-danger">
                                    {{ trans('cruds.exercise.fields.creating') }}
                                </a>
                            @endif
                            <a href="{{route('admin.reading.part3.index',['id'=>$reading_exercise->id,'exercise_id'=>$reading_exercise->exercise_id])}}" class="card c-header border-none w-100 pt-5 pb-5 d-flex justify-content-center align-items-center"  style="border-radius: 8px;">
                                <h4 class="card-title">
                                    Part Three
                                </h4>
                            </a>
                        </div>
                        <div class="col-6 "  draggable="true">
                            @if(count($reading_exercise->part_four->tests->toArray())>=9)
                                <a class="btn btn-xs process text-white btn-info">
                                    {{ trans('cruds.exercise.fields.completed') }}
                                </a>
                            @elseif(count($reading_exercise->part_four->tests->toArray())>=1)
                                <a  class="btn btn-xs process btn-warning">
                                    {{ trans('cruds.exercise.fields.process') }}
                                </a>
                            @else
                                <a class="btn btn-xs process text-white btn-danger">
                                    {{ trans('cruds.exercise.fields.creating') }}
                                </a>
                            @endif
                            <a href="{{route('admin.reading.part4.index',['id'=>$reading_exercise->id,'exercise_id'=>$reading_exercise->exercise_id])}}" class="card c-header border-none w-100 pt-5 pb-5 d-flex justify-content-center align-items-center"  style="border-radius: 8px;">
                                <h4 class="card-title">
                                    Part Four
                                </h4>
                            </a>
                        </div>
                        <div  class="col-12 "  draggable="true">
                            @if(count($reading_exercise->part_five->tests->toArray())>=2 && count($reading_exercise->part_five->answers->toArray())>=4)
                                <a class="btn btn-xs process text-white btn-info">
                                    {{ trans('cruds.exercise.fields.completed') }}
                                </a>
                            @elseif(count($reading_exercise->part_five->tests->toArray())>=1)
                                <a  class="btn btn-xs process btn-warning">
                                    {{ trans('cruds.exercise.fields.process') }}
                                </a>
                            @else
                                <a class="btn btn-xs process text-white btn-danger">
                                    {{ trans('cruds.exercise.fields.creating') }}
                                </a>
                            @endif
                            <a href="{{route('admin.reading.part5.index',['id'=>$reading_exercise->id,'exercise_id'=>$reading_exercise->exercise_id])}}" class="card c-header border-none w-100 pt-5 pb-5 d-flex justify-content-center align-items-center"  style="border-radius: 8px;">
                                <h4 class="card-title">
                                    Part Five
                                </h4>
                            </a>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>

@endsection
