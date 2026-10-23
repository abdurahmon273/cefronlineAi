@extends('layouts.admin')

@section('content')
    @can('course_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                @if($speaking_exercise->part_category=='2')
                    <a class="btn btn-default" href="{{ route('admin.speakings_exercise.part_two.index',['id'=>$speaking_exercise->exercise_id]) }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                @elseif($speaking_exercise->part_category=='5')
                    <a class="btn btn-default" href="{{ route('admin.speaking_full_exercise.index',['id'=>$speaking_exercise->id]) }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                @elseif($speaking_exercise->part_category=='4')
                    <a class="btn btn-default" href="{{ route('admin.speakings_exercise.full.index',['id'=>$speaking_exercise->exercise_id]) }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                @endif

                  <a class="btn btn-success" href="{{ route('admin.speaking.part2.question.create' ,['id'=>$speaking_exercise->id] ) }}">
                     {{ trans('global.add') }} {{  trans('cruds.speaking_part_one.fields.question') }}
                  </a>
                  <a class="btn btn-success" href="{{ route('admin.speaking.part2.image.create' ,['id'=>$speaking_exercise->id] ) }}">
                     {{ trans('global.add') }} {{  trans('cruds.speaking_part_one.fields.image') }}
                  </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            <p style="font-size: 30px">{{ trans('cruds.speaking.title_part_two') }} </p>
        </div>

        <div class="card-body">
            <livewire:admin.speaking.part-two-index-livewire :speaking_exercise="$speaking_exercise" ></livewire:admin.speaking.part-two-index-livewire>

        </div>
    </div>

@endsection
