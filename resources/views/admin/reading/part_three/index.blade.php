@extends('layouts.admin')

@section('content')
    @can('course_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                @if($reading_exercise->part_category=='3')
                    <a class="btn btn-default" href="{{ route('admin.readings.exercise.index',['id'=>$reading_exercise->exercise_id]) }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                @elseif($reading_exercise->part_category=='6')
                    <a class="btn btn-default" href="{{ route('admin.reading_full_exercise.index',['id'=>$reading_exercise->id]) }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                @endif

                  <a class="btn btn-success" href="{{ route('admin.part3.text.create' ,['id'=>$reading_exercise->id] ) }}">
                     {{ trans('global.add') }} {{ trans('cruds.reading_part_three.title_text') }}
                    </a>
                  <a class="btn btn-success" href="{{ route('admin.part3.heading.create' ,['id'=>$reading_exercise->id] ) }}">
                     {{ trans('global.add') }} {{ trans('cruds.reading_part_three.title_answer') }}
                    </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            <p style="font-size: 30px">{{  trans('cruds.reading_part_three.title_singular') }} </p>
        </div>

        <div class="card-body">
            <livewire:admin.reading.part-three-index-livewire :reading_exercise="$reading_exercise" ></livewire:admin.reading.part-three-index-livewire>

        </div>
    </div>

@endsection
