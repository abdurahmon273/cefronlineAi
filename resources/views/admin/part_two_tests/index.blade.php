@extends('layouts.admin')

@section('content')
    @can('course_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                @if($listening_exercise->part_category=='2')
                    <a class="btn btn-default" href="{{ route('admin.listenings.exercise.index',['id'=>$listening_exercise->exercise_id]) }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                @elseif($listening_exercise->part_category=='7')
                    <a class="btn btn-default" href="{{ route('admin.listening_full_exercise.index',['id'=>$listening_exercise->id]) }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                @endif

                  <a class="btn btn-success" href="{{ route('admin.part2.test.create' ,['id'=>$listening_exercise->id] ) }}">
                     {{ trans('global.add') }} {{ $listening_exercise->listening_part_two_six_question->type=='0' ? trans('cruds.listening_part_one.par2_title'):trans('cruds.listening_part_one.par2_title_1') }}
                  </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            <p style="font-size: 30px">{{ $listening_exercise->listening_part_two_six_question->type=='0' ? trans('cruds.listening_part_two_test.title'):trans('cruds.listening_part_two_test.title_1') }} </p>
        </div>

        <div class="card-body">
            <livewire:admin.listening-part-two-six-question.index-livewire :listeningExercise="$listening_exercise" :text="$Text"></livewire:admin.listening-part-two-six-question.index-livewire>

        </div>
    </div>

@endsection
