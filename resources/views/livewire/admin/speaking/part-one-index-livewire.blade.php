
<div class="table-responsive">
    <h2>1.1 Questions</h2>
    <table class=" mt-3 pb-2 table table-bordered table-striped table-hover datatable datatable-Lesson">
        <thead>
        <tr>
            <th width="10">
                N
            </th>

            <th >
                {{ trans('cruds.speaking_part_one.fields.question') }}
            </th>
            <th >
                {{ trans('cruds.speaking_part_one.fields.modal_answer') }}
            </th>
            <th  style="width: 20%">
                {{ trans('cruds.speaking_part_one.fields.audio') }}
            </th>


            <th style="width: 15%">
                &nbsp;
            </th>
        </tr>
        </thead>
        <tbody>

         @foreach($speaking_exercise->part_one_first->questions as $key=>$question)
            <tr>
                <td>
                    {{$key+1}}
                </td>
                <td>
                    {{$question->question}}
                </td>
                <td>
                    {{$question->modal_answer}}
                </td>
                <td>
                    @if($question->audio_url)
                        <audio controls style="width: 200px;">
                            <source src="{{ route('admin.speaking.audio.stream', $question->audio_url) }}" type="audio/mpeg">
                        </audio>
                    @else
                        <span class="text-danger">No audio</span>
                    @endif
                </td>

                <td >

                    {{--            @can('lesson_edit')--}}
                    <a class="btn btn-xs btn-info"
                       href="{{ route('admin.speaking.part1.1.question.edit', ['id'=>$question->id,'speaking_exercise_id'=>$speaking_exercise->id]) }}">
                        {{ trans('global.edit') }}
                    </a>
                    {{--            @endcan--}}

                    {{--            @can('lesson_delete')--}}
                    <form action="{{ route('admin.speaking.part1.1.question.destroy', ['id'=>$question->id]) }} " method="POST"
                          onsubmit="    return confirm('{{ trans('global.areYouSure') }}');"
                          style="display: inline-block;">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="submit" class="btn btn-xs btn-danger"
                               value="{{ trans('global.delete') }}">
                    </form>
                    {{--            @endcan--}}

                </td>
            </tr>
        @endforeach

        </tbody>
    </table>
    <h2>1.2 Questions</h2>
    <table class=" mt-3 pb-2 table table-bordered table-striped table-hover datatable datatable-Lesson">
        <thead>
        <tr>
            <th width="10">
                N
            </th>

            <th >
                {{ trans('cruds.speaking_part_one.fields.question') }}
            </th>
            <th >
                {{ trans('cruds.speaking_part_one.fields.modal_answer') }}
            </th>
            <th  style="width: 20%">
                {{ trans('cruds.speaking_part_one.fields.audio') }}
            </th>


            <th style="width: 15%">
                &nbsp;
            </th>
        </tr>
        </thead>
        <tbody>

         @foreach($speaking_exercise->part_one_second->questions as $key=>$question)
            <tr>
                <td>
                    {{$key+1}}
                </td>
                <td>
                    {{$question->question}}
                </td>
                <td>
                    {{$question->modal_answer}}
                </td>
                <td>
                    @if($question->audio_url)
                        <audio controls style="width: 200px;">
                            <source src="{{ route('admin.speaking.audio.stream', $question->audio_url) }}" type="audio/mpeg">
                        </audio>
                    @else
                        <span class="text-danger">No audio</span>
                    @endif
                </td>
                <td >

                    {{--            @can('lesson_edit')--}}
                    <a class="btn btn-xs btn-info"
                       href="{{ route('admin.speaking.part1.2.question.edit', ['id'=>$question->id,'speaking_exercise_id'=>$speaking_exercise->id]) }}">
                        {{ trans('global.edit') }}
                    </a>
                    {{--            @endcan--}}

                    {{--            @can('lesson_delete')--}}
                    <form action="{{ route('admin.speaking.part1.2.question.destroy', ['id'=>$question->id]) }} " method="POST"
                          onsubmit="    return confirm('{{ trans('global.areYouSure') }}');"
                          style="display: inline-block;">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="submit" class="btn btn-xs btn-danger"
                               value="{{ trans('global.delete') }}">
                    </form>
                    {{--            @endcan--}}

                </td>
            </tr>
        @endforeach

        </tbody>
    </table>

    <h2>Images</h2>
    <table class="mt-3 table table-bordered table-striped table-hover datatable datatable-Lesson">
        <thead>
        <tr>
            <th width="10">
                N
            </th>

            <th >
                {{ trans('cruds.speaking_part_one.fields.image') }}
            </th>


            <th style="width: 15%;">
                &nbsp;
            </th>
        </tr>
        </thead>
        <tbody>

         @foreach($speaking_exercise->part_one_second->images as $key=>$image)
            <tr>
                <td>
                    {{$key+1}}
                </td>
                <td>
                  <div class="">
                      <img class=" w-50" src="{{ Storage::url('public/speaking_images/'.$image->image) }}"  alt="">
                  </div>
                </td>

                <td>

                    {{--            @can('lesson_edit')--}}
                    <a class="btn btn-xs btn-info"
                       href="{{ route('admin.speaking.part1.2.image.edit', ['id'=>$image->id,'speaking_exercise_id'=>$speaking_exercise->id]) }}">
                        {{ trans('global.edit') }}
                    </a>
                    {{--            @endcan--}}

                    {{--            @can('lesson_delete')--}}
                    <form action="{{ route('admin.speaking.part1.2.image.destroy', ['id'=>$image->id]) }} " method="POST"
                          onsubmit="    return confirm('{{ trans('global.areYouSure') }}');"
                          style="display: inline-block;">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="submit" class="btn btn-xs btn-danger"
                               value="{{ trans('global.delete') }}">
                    </form>
                    {{--            @endcan--}}

                </td>
            </tr>
        @endforeach

        </tbody>
    </table>
</div>
