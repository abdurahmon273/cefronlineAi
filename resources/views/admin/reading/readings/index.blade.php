@extends('layouts.admin')

@section('content')
    @can('course_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.reading.exercises.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.reading.title') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            <p style="font-size: 30px">{{ trans('cruds.reading.title') }} {{ trans('cruds.reading.fields.exercises') }}</p>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-Lesson">
                    <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.lesson.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.lesson.fields.name') }}
                        </th>
                        <th>
                            {{ trans('cruds.lesson.fields.status') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                    </thead>
                    <livewire:admin.reading-index-livewire :exercises="$excercises"></livewire:admin.reading-index-livewire>
                </table>
            </div>
        </div>
    </div>

@endsection
