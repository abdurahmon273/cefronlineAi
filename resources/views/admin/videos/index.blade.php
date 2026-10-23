@extends('layouts.admin')
@section('content')
@can('video_create')
    <div class="form-group">
        <a class="btn btn-default" href="{{ route('admin.lessons.index2',['id'=>$course]) }}">
            {{ trans('global.back_to_list') }}
        </a>

        <a class="btn btn-success" href="{{ route('admin.videos.create2',['id'=>$id]) }}">
            {{ trans('global.add') }} {{ trans('cruds.video.title') }}
        </a>

    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.video.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Video">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.video.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.course.fields.name') }}
                        </th>
                        <th>
                            {{ trans('cruds.video.fields.link') }}
                        </th>
                        <th>
                            {{ trans('cruds.video.fields.lesson') }}
                        </th>
                        <th>
                            {{ trans('cruds.video.fields.status') }}
                        </th>
                        <th>
                            {{ trans('cruds.video.fields.files') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <livewire:videos.index-livewire :videos="$videos" :id="$id"></livewire:videos.index-livewire>
            </table>
        </div>
    </div>
</div>



@endsection
@section('scripts')
@parent
{{--<script>--}}
{{--    $(function () {--}}
{{--  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)--}}
{{--@can('video_delete')--}}
{{--  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'--}}
{{--  let deleteButton = {--}}
{{--    text: deleteButtonTrans,--}}
{{--    url: "{{ route('admin.videos.massDestroy') }}",--}}
{{--    className: 'btn-danger',--}}
{{--    action: function (e, dt, node, config) {--}}
{{--      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {--}}
{{--          return $(entry).data('entry-id')--}}
{{--      });--}}

{{--      if (ids.length === 0) {--}}
{{--        alert('{{ trans('global.datatables.zero_selected') }}')--}}

{{--        return--}}
{{--      }--}}

{{--      if (confirm('{{ trans('global.areYouSure') }}')) {--}}
{{--        $.ajax({--}}
{{--          headers: {'x-csrf-token': _token},--}}
{{--          method: 'POST',--}}
{{--          url: config.url,--}}
{{--          data: { ids: ids, _method: 'DELETE' }})--}}
{{--          .done(function () { location.reload() })--}}
{{--      }--}}
{{--    }--}}
{{--  }--}}
{{--  dtButtons.push(deleteButton)--}}
{{--@endcan--}}

{{--  $.extend(true, $.fn.dataTable.defaults, {--}}
{{--    orderCellsTop: true,--}}
{{--    order: [[ 1, 'desc' ]],--}}
{{--    pageLength: 100,--}}
{{--  });--}}
{{--  let table = $('.datatable-Video:not(.ajaxTable)').DataTable({ buttons: dtButtons })--}}
{{--  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){--}}
{{--      $($.fn.dataTable.tables(true)).DataTable()--}}
{{--          .columns.adjust();--}}
{{--  });--}}

{{--})--}}

{{--</script>--}}
@endsection
