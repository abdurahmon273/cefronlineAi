@extends('layouts.admin')

@section('content')
    @can('course_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                @if($listening_exercise->part_category=='3')
                    <a class="btn btn-default" href="{{ route('admin.listenings.exercise.index',['id'=>$listening_exercise->exercise_id]) }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                @elseif($listening_exercise->part_category=='7')
                    <a class="btn btn-default" href="{{ route('admin.listening_full_exercise.index',['id'=>$listening_exercise->id]) }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                @endif

                  <a class="btn btn-success" href="{{ route('admin.part3.speaker.create' ,['id'=>$listening_exercise->id] ) }}">
                     {{ trans('global.add') }} {{ trans('cruds.listening_part_three.add_title')}}
                    </a>
                  <a class="btn btn-success" href="{{ route('admin.part3.answer.create' ,['id'=>$listening_exercise->id] ) }}">
                     {{ trans('global.add') }} {{ trans('cruds.listening_part_three.add_title1')}}
                  </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            <p style="font-size: 30px">{{ trans('cruds.listening_part_three.title') }} </p>
        </div>

        <div class="card-body">
            <livewire:admin.listening-part-three-four.index-livewire :listeningExercise="$listening_exercise" :text="$Text"></livewire:admin.listening-part-three-four.index-livewire>

        </div>
    </div>

@endsection
