@extends('layouts.admin')

@section('content')
    @can('course_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">

                    @if($question->listeningExercise->part_category=='5')
                        <a class="btn btn-default" href="{{ route('admin.listenings.exercise.index',['id'=>$exercise_id]) }}">
                            {{ trans('global.back_to_list') }}
                        </a>
                    @elseif($question->listeningExercise->part_category=='7')
                        <a class="btn btn-default" href="{{ route('admin.listening_full_exercise.index',['id'=>$listening_id]) }}">
                            {{ trans('global.back_to_list') }}
                        </a>
                    @endif
                <a class="btn btn-success" href="{{ route('admin.listenings.part5.create' ,['id'=>$question_id,'id1'=>$listening_id,'id2'=>$exercise_id]) }}">
                    {{ trans('global.add') }} {{ trans('cruds.listening_part_five_test.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            <p style="font-size: 30px">{{ trans('cruds.listening_part_five_test.title') }} </p>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-Lesson">
                    <thead>
                    <tr>
                        <th width="10">
                        N
                        </th>
{{--                        <th>--}}
{{--                            {{ trans('cruds.listening_part_one_test.fields.id') }}--}}
{{--                        </th>--}}

                        <th style="width: 28%;">
                            {{ trans('cruds.listening_part_one_test.fields.question') }}
                        </th>
                        <th style="width: 20%;">
                            {{ trans('cruds.listening_part_one_test.fields.answer1') }}
                        </th>
                        <th style="width: 20%;">
                            {{ trans('cruds.listening_part_one_test.fields.answer2') }}
                        </th>
                        <th style="width: 20%;">
                            {{ trans('cruds.listening_part_one_test.fields.answer3') }}
                        </th>
                        <th style="width: 15%;">
                            &nbsp;
                        </th>
                    </tr>
                    </thead>
                    <livewire:admin.listening-part-five-question.index-livewire :tests="$tests"  :question_id="$question_id"  ></livewire:admin.listening-part-five-question.index-livewire>
                </table>
            </div>
        </div>
    </div>

@endsection
