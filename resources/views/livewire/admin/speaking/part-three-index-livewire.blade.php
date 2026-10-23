
<div class="table-responsive">
    <h2>{{ trans('cruds.speaking_part_three.fields.theme') }}</h2>
     <div class=" mb-3">
         <h4>{{$speaking_exercise->part_three->theme}}?</h4>
     </div>
    <hr>


    <h2>{{ trans('cruds.speaking_part_three.fields.question_text') }}</h2>
    <table class=" table table-bordered table-striped table-hover datatable datatable-Lesson mb-5">
        <thead>
        <tr>

            <th>
                Text
            </th>
            <th style="width: 20%">
                Audio
            </th>
            <th style="width: 10%">
                &nbsp;
            </th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>
                <textarea class="form-control" disabled  style="font-size: 18px" rows="8"> {{$speaking_exercise->part_three->question_text}} </textarea>
            </td>

            <td>
                @if($speaking_exercise->part_three->audio_url)
                    <audio controls style="width: 200px;">
                        <source src="{{ route('admin.speaking.audio.stream', $speaking_exercise->part_three->audio_url) }}" type="audio/mpeg">
                    </audio>
                @else
                    <span class="text-danger">No audio</span>
                @endif
            </td>
            <td>
                <a class="btn btn-xs btn-info"
                   href="{{ route('admin.part3.speaking.text.edit', $speaking_exercise->part_three->id) }}">
                    {{ trans('global.edit') }}
                </a>
            </td>
        </tr>
        </tbody>
    </table>

   {{-- <h2>{{ trans('cruds.speaking_part_three.fields.for') }}</h2>
    <table class=" mt-3 pb-2 table table-bordered table-striped table-hover datatable datatable-Lesson">
        <thead>
        <tr>
            <th width="10">
                N
            </th>

            <th >
                {{ trans('cruds.speaking_part_three.fields.detail') }}
            </th>


            <th style="width: 15%">
                &nbsp;
            </th>
        </tr>
        </thead>
        <tbody>

        @foreach($speaking_exercise->part_three->questions->where('type','0') as $key=>$question)
            <tr>
                <td>
                    {{$key+1}}
                </td>
                <td>
                    {{$question->question}}
                </td>

                <td >

                    --}}{{--            @can('lesson_edit')--}}{{--
                    <a class="btn btn-xs btn-info"
                       href="{{ route('admin.speaking.part3.question.edit', ['id'=>$question->id,'speaking_exercise_id'=>$speaking_exercise->id]) }}">
                        {{ trans('global.edit') }}
                    </a>
                    --}}{{--            @endcan--}}{{--

                    --}}{{--            @can('lesson_delete')--}}{{--
                    <form action="{{ route('admin.speaking.part3.question.destroy', ['id'=>$question->id]) }} " method="POST"
                          onsubmit="    return confirm('{{ trans('global.areYouSure') }}');"
                          style="display: inline-block;">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="submit" class="btn btn-xs btn-danger"
                               value="{{ trans('global.delete') }}">
                    </form>
                    --}}{{--            @endcan--}}{{--

                </td>
            </tr>
        @endforeach

        </tbody>
    </table>
    <hr>
    <h2>{{ trans('cruds.speaking_part_three.fields.againsts') }}</h2>
    <table class=" mt-3 pb-2 table table-bordered table-striped table-hover datatable datatable-Lesson">
        <thead>
        <tr>
            <th width="10">
                N
            </th>

            <th >
                {{ trans('cruds.speaking_part_three.fields.detail') }}
            </th>


            <th style="width: 15%">
                &nbsp;
            </th>
        </tr>
        </thead>
        <tbody>

        @foreach($speaking_exercise->part_three->questions->where('type','1') as $key=>$question)
            <tr>
                <td>
                    {{$key+1}}
                </td>
                <td>
                    {{$question->question}}
                </td>

                <td >

                    --}}{{--            @can('lesson_edit')--}}{{--
                    <a class="btn btn-xs btn-info"
                       href="{{ route('admin.speaking.part3.against.edit', ['id'=>$question->id,'speaking_exercise_id'=>$speaking_exercise->id]) }}">
                        {{ trans('global.edit') }}
                    </a>
                    --}}{{--            @endcan--}}{{--

                    --}}{{--            @can('lesson_delete')--}}{{--
                    <form action="{{ route('admin.speaking.part3.against.destroy', ['id'=>$question->id]) }} " method="POST"
                          onsubmit="    return confirm('{{ trans('global.areYouSure') }}');"
                          style="display: inline-block;">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="submit" class="btn btn-xs btn-danger"
                               value="{{ trans('global.delete') }}">
                    </form>
                    --}}{{--            @endcan--}}{{--

                </td>
            </tr>
        @endforeach

        </tbody>
    </table>--}}

</div>
