@extends('layouts.admin')
@section('content')
@can('saved_lesson_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.saved-lessons.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.savedLesson.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.savedLesson.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-SavedLesson">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.savedLesson.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.savedLesson.fields.user') }}
                        </th>
                        <th>
                            {{ trans('cruds.savedLesson.fields.saved') }}
                        </th>
                        <th>
                            {{ trans('cruds.savedLesson.fields.liked') }}
                        </th>
                        <th>
                            {{ trans('cruds.savedLesson.fields.video') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($savedLessons as $key => $savedLesson)
                        <tr data-entry-id="{{ $savedLesson->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $savedLesson->id ?? '' }}
                            </td>
                            <td>
                                {{ $savedLesson->user->name ?? '' }}
                            </td>
                            <td>
                                {{ App\Models\SavedLesson::SAVED_SELECT[$savedLesson->saved] ?? '' }}
                            </td>
                            <td>
                                {{ App\Models\SavedLesson::LIKED_SELECT[$savedLesson->liked] ?? '' }}
                            </td>
                            <td>
                                {{ $savedLesson->video->link ?? '' }}
                            </td>
                            <td>
                                @can('saved_lesson_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.saved-lessons.show', $savedLesson->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('saved_lesson_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.saved-lessons.edit', $savedLesson->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('saved_lesson_delete')
                                    <form action="{{ route('admin.saved-lessons.destroy', $savedLesson->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                    </form>
                                @endcan

                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>



@endsection
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('saved_lesson_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.saved-lessons.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
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
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
      }
    }
  }
  dtButtons.push(deleteButton)
@endcan

  $.extend(true, $.fn.dataTable.defaults, {
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  });
  let table = $('.datatable-SavedLesson:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection