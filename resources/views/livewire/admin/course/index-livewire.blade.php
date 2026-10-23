<div class="row row-cols-md-6 g-4" wire:sortable="updateOrder">
    @foreach($courses as $key => $course)
        <div class="col" wire:sortable.item="{{$course->id}}" draggable="true">

            <div class="card c-header" style="border-radius: 8px;">
                <div class="dropdown position-absolute" style="top: 0px; right: 1px;font-size: large;">
                    <a href="#" class="nav-link text-dark" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v" style="color: white; font-size: large;"></i></a>
                    <ul class="dropdown-menu dropdown-menu-right border-0 shadow" style="">
                        <li>
                            <a class="dropdown-item d-flex justify-content-between text-info align-items-center" href="{{route('admin.courses.edit',[$course->id])}}"><span>Edit</span><i class="fa fa-pencil"></i></a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex justify-content-between text-success align-items-center" href="{{route('admin.courses.show',[$course->id])}}"><span>Show</span> <i class="fa fa-eye"></i></a>
                        </li>
                        <li>
                            <form style="cursor: pointer;" action="{{ route('admin.courses.destroy', $course->id) }}" method="POST"
                                  onsubmit="return confirm('{{ trans('global.areYouSure') }}');">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <li class="justify-content-between d-flex" style="margin: 0 auto; width: 85%">
                                    <input type="submit" class="btn-outline-none text-danger border-0 bg-transparent"
                                           value="{{ trans('global.delete') }}">
                                    <i  style="padding:2px 10px 0 0; " class=" fa fa-trash text-danger " ></i>
                                </li>
                            </form>
                        </li>
                    </ul>
                </div>
                <div></div>
                @if($course->image)
                    <img wire:sortable.handle style="width: 230px; padding: 10px" src="{{ $course->image->getUrl('preview') }}">
                @endif
                <div class="" style="padding-left: 10px; padding-bottom: 20px ">
                    <h4 class="card-title"><a style="color: black; font-weight: 500"
                                              href="{{ route('admin.lessons.index2',['id'=>$course->id]) }}">
                            {{ $course->name ?? '' }}
                        </a></h4>
                    <p class="card-text" style="font-weight: 400"> {{ App\Models\Course::PUBLISHED_SELECT[$course->published] ?? '' }}</p>
                    <p class="card-text" style="font-weight: 400"> {{ App\Models\Course::STATUS[$course->status] ?? ' ' }}</p>
                </div>
            </div>
        </div>
    @endforeach
</div>
