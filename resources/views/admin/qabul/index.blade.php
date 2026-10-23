@extends('layouts.admin')
@section('content')
    @can('news_create')

    @endcan
    <div class="card">
        <div class="card-header">
            Join Course
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.newss.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class=" table table-bordered  table-hover datatable datatable-User">
                    <thead>
                    <tr>
                        <th width="10">
                            #
                        </th>
                        <th>
                            {{ trans('cruds.user.fields.name') }}
                        </th>
                        <th>
                            {{ trans('cruds.news.title') }}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($qabuls as $key => $qabul)
                        <tr data-entry-id="{{ $qabul->id }}">
                            <td>
                                {{$loop->iteration}}
                            </td>
                            <td>
                                {{$qabul->user->name ?? '' }} {{$qabul->user?->surname ?? ''}}
                            </td>
                            <td>
                                {{ $qabul->news->title ?? '' }}
                            </td>

                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{$qabuls->links()}}
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
