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


    </style>
@endsection
@section('content')

    <div class="card">
        <div class="card-header">
            <p style="font-size: 30px">{{ trans('cruds.writing.title') }} {{ trans('cruds.writing.fields.exercises') }}</p>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <div>

                    <div class="row ">
                        <div class="col-6 "  draggable="true">
                            <a href="{{route('admin.speaking.part1.exercise.index')}}" class="card c-header border-none w-100 pt-5 pb-5 d-flex justify-content-center align-items-center"  style="border-radius: 8px;">
                                <h4 class="card-title">
                                    Listening
                                </h4>
                            </a>
                        </div>
                        <div  class="col-6 "  draggable="true">
                            <a href="{{route('admin.speaking.part2.exercise.index')}}" class="card c-header border-none w-100 pt-5 pb-5 d-flex justify-content-center align-items-center"  style="border-radius: 8px;">
                                <h4 class="card-title">
                                    Reading
                                </h4>
                            </a>
                        </div>
                        <div  class="col-6 "  draggable="true">
                            <a href="{{route('admin.speaking.part3.exercise.index')}}" class="card c-header border-none w-100 pt-5 pb-5 d-flex justify-content-center align-items-center"  style="border-radius: 8px;">
                                <h4 class="card-title">
                                    Writing
                                </h4>
                            </a>
                        </div>
                        <div class="col-6 "  draggable="true">
                            <a href="{{route('admin.speaking.full.exercise.index')}}" class="card c-header border-none w-100 pt-5 pb-5 d-flex justify-content-center align-items-center"  style="border-radius: 8px;">
                                <h4 class="card-title">
                                    Speaking
                                </h4>
                            </a>
                        </div>
                        <div class="col-12 "  draggable="true">
                            <a href="{{route('admin.speaking.full.exercise.index')}}" class="card c-header border-none w-100 pt-5 pb-5 d-flex justify-content-center align-items-center"  style="border-radius: 8px;">
                                <h4 class="card-title">
                                    Full Mock
                                </h4>
                            </a>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>

@endsection
