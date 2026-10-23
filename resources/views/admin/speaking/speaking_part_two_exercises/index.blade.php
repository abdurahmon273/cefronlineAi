@extends('layouts.admin')

@section('content')
    @can('course_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-default" href="{{ route('admin.speaking.part2.exercise.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
                <a class="btn btn-success" href="{{ route('admin.speakings_exercise.part_two.create' ,['id'=>$id]) }}">
                    {{ trans('global.add') }} {{ trans('cruds.Speaking_exercise.title') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            <p style="font-size: 30px">{{ trans('cruds.speaking.title_part_two') }} </p>
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
                            {{ trans('cruds.lesson.fields.status') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                    </thead>
                    <livewire:admin.speaking.part-two-speaking-exercises-livewire  :id="$id"  ></livewire:admin.speaking.part-two-speaking-exercises-livewire>
                </table>
            </div>
        </div>
    </div>

@endsection
