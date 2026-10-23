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
    @can('course_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-default" href="{{ route('admin.writing.exercises.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
                <a class="btn btn-success" href="{{ route('admin.writing.task2.exercises.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.writing.title') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            <p style="font-size: 30px">{{ trans('cruds.writing.title_task_two') }}</p>
        </div>

        <div class="card-body">
            <div class="table-responsive">

                @livewire('admin.writing.task-two-writing-index-livewire')

            </div>
        </div>
    </div>

@endsection
