@extends('layouts.admin')

@section('content')
    @can('course_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-default" href="{{ route('admin.speaking.exercises.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
                <a class="btn btn-success" href="{{ route('admin.speaking.part3.exercises.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.speaking.title') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            <p style="font-size: 30px">{{ trans('cruds.speaking.title_part_three') }} </p>
        </div>

        <div class="card-body">
            <div class="table-responsive">

                    @livewire('admin.speaking.part-three-speaking-livewire')

            </div>
        </div>
    </div>

@endsection
