@extends('layouts.admin')

@section('content')
    @can('course_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-default" href="{{ route('admin.writing.exercises.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
                <a class="btn btn-success" href="{{ route('admin.writing.full.exercises.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.writing.title') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            <p style="font-size: 30px">{{ trans('cruds.writing.title_full') }} </p>
        </div>

        <div class="card-body">
            <div class="table-responsive">

                @livewire('admin.writing.task-full-writing-index-livewire')

            </div>
        </div>
    </div>

@endsection
