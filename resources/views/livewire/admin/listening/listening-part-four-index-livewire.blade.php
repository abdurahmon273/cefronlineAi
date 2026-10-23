
<div class="table-responsive">

    <div class="w-100 mb-3">
        <a class="btn btn-xs btn-info  " style="position: absolute"
           href="{{ route('admin.part4.map.edit', ['id'=>$listeningExercise->part_four->id,'listening_id'=>$listeningExercise->id] )}}">
            {{ trans('global.edit') }}
        </a>
        <img src="{{ Storage::url($listeningExercise->part_four->image ? 'public/maps/'.$listeningExercise->part_four->image : 'public/maps/default.png')}}" alt="" class="form-control w-100 " style="height: 500px">
    </div>

    <table class=" table table-bordered table-striped table-hover datatable datatable-Lesson mb-5">
        <thead>
        <tr>

            <th>
                {{ trans('cruds.listening_part_four.fields.questions') }}
            </th>
            <th>
                {{ trans('cruds.listening_part_four.fields.locations') }}
            </th>

        </tr>
        </thead>
        <tbody>
        <tr>
            <td>
                @php $letter_array=\App\Models\ListeningPartThreeFourAnswers::LETTERS @endphp

                @foreach($listeningExercise->part_four->part_three_four_questions as $speaker)
                    <span style="font-size: 18px" > {{$speaker->title}} -> {{$letter_array[$speaker->correct_answer->letter]}} </span> <br>
                @endforeach

            </td>
            <td>
                @foreach($listeningExercise->part_four->part_three_four_answers as $answer)
                    <span class="d-flex mt-0 mb-0 w-100 " style="font-size: 18px; margin: 0px;padding: 0px;" >
                        <span class="w-100">
                        {{$letter_array[$answer->letter]}} {{ $answer->answer ?($answer->answer!==' ' ?  '('.$answer->answer.')' : '') : ' ' }}
                        </span>
                        <a class=" text-info  " href="{{route('admin.part4.location.edit',['id'=>$answer->id,'listening_id'=>$listeningExercise->id])}}"><i class="fa fa-pencil"></i></a>
                           <form style="cursor: pointer;" action="{{ route('admin.part4.location.destroy', $answer->id) }}" method="POST"
                                 onsubmit="return confirm('{{ $answer->question ? trans('global.cancel_delete') : trans('global.areYouSure') }}');">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <li class="justify-content-start d-flex" style="margin: 0 auto; width: 85%">
                                    <button type="submit" class="btn-outline-none text-danger border-0 bg-transparent"
                                            value="">

                                    <i  style="padding:2px 10px 0 0; " class=" fa fa-trash text-danger " ></i>
                                    </button>
                                </li>
                            </form>
                    </span>
                @endforeach

            </td>

        </tr>
        </tbody>
    </table>
    <div class="w-100">
        <table class=" table table-bordered table-striped table-hover datatable datatable-Lesson ">
            <thead>
            <tr>
                <th width="10">
                    N
                </th>

                <th>
                    {{ trans('cruds.listening_part_four.add_title') }}
                </th>


                <th>
                    {{ trans('cruds.listening_part_one_test.fields.correct_answer') }}
                </th>
                <th style="width: 12%">
                    &nbsp;
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach($listeningExercise->part_four->part_three_four_questions as $speaker)

                <tr>
                    <td></td>
                    <td>{{$speaker->title}}</td>
                    <td>{{$letter_array[$speaker->correct_answer->letter]}} {{$speaker->correct_answer->answer && $speaker->correct_answer->answer!==' ' ? '('.$speaker->correct_answer->answer.')': ''}}</td>
                    <td>
                        {{--            @can('lesson_edit')--}}
                        <a class="btn btn-xs btn-info"
                           href="{{ route('admin.part4.question.edit', ['id'=>$speaker->id,'listening_id'=>$listeningExercise->id] )}}">
                            {{ trans('global.edit') }}
                        </a>
                        {{--            @endcan--}}

                        {{--            @can('lesson_delete')--}}
                        <form action="{{ route('admin.part3.speaker.destroy', $speaker->id )}} " method="POST"
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
</div>
