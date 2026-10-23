@extends('layouts.admin')

@section('content')
    @can('course_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-default" href="{{ route('admin.full.exercises.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
                <a class="btn btn-success" href="{{ route('admin.full_exercise.create' ,['id'=>$id]) }}">
                    {{ trans('global.add') }} {{ trans('cruds.exercise.full_exercise') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            <p style="font-size: 30px">{{ trans('cruds.exercise.full') }} </p>
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
                            {{ trans('cruds.lesson.fields.title') }}
                        </th>

                        <th>
                            {{ trans('cruds.lesson.fields.status') }}
                        </th>
                        <th style="width: 15%">
                            &nbsp;
                        </th>
                    </tr>
                    </thead>

                    <livewire:admin.full.full-exercise-livewire  :id="$id" ></livewire:admin.full.full-exercise-livewire>

                </table>
            </div>
        </div>
    </div>

@endsection
