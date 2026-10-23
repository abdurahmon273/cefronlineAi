@extends('layouts.admin')

@section('content')
    @can('course_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                @if($reading_exercise->part_category=='5')
                    <a class="btn btn-default" href="{{ route('admin.readings.exercise.index',['id'=>$reading_exercise->exercise_id]) }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                @elseif($reading_exercise->part_category=='6')
                    <a class="btn btn-default" href="{{ route('admin.reading_full_exercise.index',['id'=>$reading_exercise->id]) }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                @endif

                  <a class="btn btn-success" href="{{ route('admin.part5.answer.create' ,['id'=>$reading_exercise->id] ) }}">
                     {{ trans('global.add') }} {{  trans('cruds.reading_part_one.fields.answer')}}
                  </a>
                  <a class="btn btn-success" href="{{ route('admin.part5.test.create' ,['id'=>$reading_exercise->id] ) }}">
                     {{ trans('global.add') }} {{  trans('cruds.reading_part_five.fields.test')}}
                  </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            <p style="font-size: 28px">{{ trans('cruds.reading_part_five.title')}} </p>
        </div>

        <div class="card-body">
            <livewire:admin.reading.part-five-index-livewire :reading_exercise="$reading_exercise" :text="$Text"></livewire:admin.reading.part-five-index-livewire>

        </div>
    </div>

@endsection
