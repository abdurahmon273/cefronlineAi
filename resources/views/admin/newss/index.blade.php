@extends('layouts.admin')
@section('content')
    @can('news_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.newss.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.news.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.news.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-Lesson">
                    <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th style="width: 5%">
                            {{ trans('cruds.news.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.news.fields.title') }}
                        </th>

                        <th style="width: 15%">
                            {{ trans('cruds.news.fields.image') }}
                        </th>
                        <th  style="width: 35%" >
                            {{ trans('cruds.news.fields.web_image') }}
                        </th>
                        <th style="width: 20%">
                            &nbsp;
                        </th>
                    </tr>
                    </thead>

                    <livewire:admin.newss.index-livewire ></livewire:admin.newss.index-livewire>
                </table>
            </div>

            <div class="row row-cols-md-6 g-4">
                @foreach([] as $key => $news)
                    <div class="col" >

                        <div class="card c-header" style="border-radius: 8px;">
                            <div class="dropdown position-absolute" style="top: 0px; right: 1px; z-index: 100">
                                <a href="#" class="nav-link text-dark" data-toggle="dropdown" aria-expanded="false"><i
                                        class="fa fa-ellipsis-v" style="color: black; font-size: inherit; margin-top: 200px"></i></a>
                                <ul class="dropdown-menu dropdown-menu-right border-0 shadow" style="">
                                    <li>
                                        <a class="dropdown-item d-flex justify-content-between text-info align-items-center"
                                           href="{{route('admin.newss.edit',[$news->id])}}"><span>Edit</span><i
                                                class="fa fa-pencil"></i></a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex justify-content-between text-success align-items-center"
                                           href="{{route('admin.newss.show',[$news->id])}}"><span>Show</span> <i
                                                class="fa fa-eye"></i></a>
                                    </li>
                                    <li>
                                        <form style="cursor: pointer;"
                                              action="{{ route('admin.newss.destroy', $news->id) }}" method="POST"
                                              onsubmit="return confirm('{{ trans('global.areYouSure') }}');">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <li class="justify-content-between d-flex"
                                                style="margin: 0 auto; width: 85%">
                                                <input type="submit"
                                                       class="btn-outline-none text-danger border-0 bg-transparent"
                                                       value="{{ trans('global.delete') }}">
                                                <i style="padding:2px 10px 0 0; " class=" fa fa-trash text-danger "></i>
                                            </li>
                                        </form>
                                    </li>
                                </ul>
                            </div>

                            @if($news->status==0)
                                <img style=" width:100%; height:200px; border-top-right-radius: 8px;border-top-left-radius: 8px;" src="{{ $news->image ?  $news->image->getUrl() : '' }}">
                            @else

                                @if(!empty($news->video_link))
                                    <iframe width="100%" style="border-radius: 8px;" height="200" src="https://www.youtube.com/embed/{{ $news->video_link }}" frameborder="0" allowfullscreen></iframe>
                                @else
                                    <iframe width="100%" style="border-radius: 8px;" height="200" src="https://www.youtube.com/embed/{{ $news->video_link }}" frameborder="0" allowfullscreen></iframe>
                                @endif
{{--                                <video width="233" height="250" controls  style="border-top-right-radius: 8px;border-top-left-radius: 8px;">--}}
{{--                                    <source src="{{\Illuminate\Support\Facades\URL::asset($news->video_link ?? '')}}" type="video/mp4">--}}
{{--                                </video>--}}
                            @endif
                            <div class="card-body">
                                <h4 class="card-title"><a style="color: black"
                                                          href="{{ route('admin.newsJoin',['id'=>$news->id]) }}">
                                        {{substr($news->title,0,30). '...'  ?? '' }}
                                    </a></h4>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    @parent
    <script>
        $(function () {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            @can('news_delete')
            let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
            let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.newss.massDestroy') }}",
                className: 'btn-danger',
                action: function (e, dt, node, config) {
                    var ids = $.map(dt.rows({selected: true}).nodes(), function (entry) {
                        return $(entry).data('entry-id')
                    });

                    if (ids.length === 0) {
                        alert('{{ trans('global.datatables.zero_selected') }}')

                        return
                    }

                    if (confirm('{{ trans('global.areYouSure') }}')) {
                        $.ajax({
                            headers: {'x-csrf-token': _token},
                            method: 'POST',
                            url: config.url,
                            data: {ids: ids, _method: 'DELETE'}
                        })
                            .done(function () {
                                location.reload()
                            })
                    }
                }
            }
            dtButtons.push(deleteButton)
            @endcan

            $.extend(true, $.fn.dataTable.defaults, {
                orderCellsTop: true,
                order: [[1, 'desc']],
                pageLength: 100,
            });
            let table = $('.datatable-News:not(.ajaxTable)').DataTable({buttons: dtButtons})
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function (e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        })

    </script>
@endsection
