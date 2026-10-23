@extends('layouts.admin')

@section('content')
    @can('course_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-default" href="{{ route('admin.writing.full.exercises.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
                <a class="btn btn-success" href="{{ route('admin.writings_exercise.full.create' ,['id'=>$id]) }}">
                    {{ trans('global.add') }} {{ trans('cruds.writing.title_full') }}
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
                <table class=" table table-bordered table-striped table-hover datatable datatable-Lesson">
                    <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.lesson.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.lesson.fields.title') }}
                        </th>
                        <th>
                            {{ trans('cruds.writing_exercise.fields.time_limit') }}
                        </th>
                        <th>
                            {{ trans('cruds.writing_exercise.fields.word_counts') }}
                        </th>
                        <th>
                            {{ trans('cruds.lesson.fields.status') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                    </thead>

                    <livewire:admin.writing.full-writing-exercise-index-livewire  :id="$id" ></livewire:admin.writing.full-writing-exercise-index-livewire>

                </table>
            </div>
        </div>
    </div>

@endsection
