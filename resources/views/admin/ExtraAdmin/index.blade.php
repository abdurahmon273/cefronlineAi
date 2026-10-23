@extends('layouts.admin')
@section('content')
    @can('user_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.extra_admins.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.extraAdmin.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    @if (session()->has('send-sms'))
        <div class="alert alert-success">
            {{ session('send-sms') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            {{ trans('cruds.extraAdmin.title_singular') }} {{ trans('global.list') }}
            <button type="button" id="smsButton" style="margin-left: 1450px" class="btn btn-success" data-toggle="modal"
                    data-target="#exampleModal">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                     class="bi bi-chat-dots" viewBox="0 0 16 16">
                    <path
                            d="M5 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0m4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0m3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/>
                    <path
                            d="m2.165 15.803.02-.004c1.83-.363 2.948-.842 3.468-1.105A9 9 0 0 0 8 15c4.418 0 8-3.134 8-7s-3.582-7-8-7-8 3.134-8 7c0 1.76.743 3.37 1.97 4.6a10.4 10.4 0 0 1-.524 2.318l-.003.011a11 11 0 0 1-.244.637c-.079.186.074.394.273.362a22 22 0 0 0 .693-.125m.8-3.108a1 1 0 0 0-.287-.801C1.618 10.83 1 9.468 1 8c0-3.192 3.004-6 7-6s7 2.808 7 6-3.004 6-7 6a8 8 0 0 1-2.088-.272 1 1 0 0 0-.711.074c-.387.196-1.24.57-2.634.893a11 11 0 0 0 .398-2"/>
                </svg>
            </button>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                @livewire('admin.users.send-sms')
            </div>
        </div>

        <div class="card-body">

            @livewire('admin.extra-admin.extra-admin-index-livewire')

        </div>
    </div>

@endsection
@section('scripts')
    @parent
{{--    <script>--}}
{{--        document.addEventListener('render', () => {--}}
{{--            document.getElementById('render').click();--}}
{{--        });--}}

{{--        document.getElementById('smsButton').addEventListener('click', () => {--}}
{{--            let ids = [];--}}
{{--            document.querySelectorAll('tbody tr.selected').forEach(e => {--}}
{{--                ids.push(e.getAttribute('data-entry-id'))--}}
{{--            });--}}
{{--            Livewire.dispatch('users_id', [ids]);--}}
{{--        });--}}

{{--        $(function () {--}}
{{--            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)--}}

{{--            @can('user_delete')--}}
{{--            let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'--}}
{{--            let deleteButton = {--}}
{{--                text: deleteButtonTrans,--}}
{{--                url: "{{ route('admin.users.massDestroy') }}",--}}
{{--                className: 'btn-danger',--}}
{{--                action: function (e, dt, node, config) {--}}
{{--                    var ids = $.map(dt.rows({selected: true}).nodes(), function (entry) {--}}
{{--                        return $(entry).data('entry-id');--}}
{{--                    });--}}

{{--                    if (ids.length === 0) {--}}
{{--                        alert('{{ trans('global.datatables.zero_selected') }}');--}}
{{--                        return;--}}
{{--                    }--}}

{{--                    if (confirm('{{ trans('global.areYouSure') }}')) {--}}
{{--                        $.ajax({--}}
{{--                            headers: {'x-csrf-token': _token},--}}
{{--                            method: 'POST',--}}
{{--                            url: config.url,--}}
{{--                            data: {ids: ids, _method: 'DELETE'}--}}
{{--                        })--}}
{{--                            .done(function () {--}}
{{--                                location.reload();--}}
{{--                            });--}}
{{--                    }--}}
{{--                }--}}
{{--            }--}}
{{--            dtButtons.push(deleteButton)--}}
{{--            @endcan--}}

{{--            $.extend(true, $.fn.dataTable.defaults, {--}}
{{--                orderCellsTop: true,--}}
{{--                order: [[1, 'desc']],--}}
{{--                pageLength: 100,--}}
{{--                searching: true, // Enable the search input--}}
{{--            });--}}

{{--            let table = $('.datatable-User:not(.ajaxTable)').DataTable({--}}
{{--                buttons: dtButtons,--}}
{{--                searching: true // Enable the search box--}}
{{--            });--}}

{{--            // Attach listener to search input box--}}
{{--            $(document).on('input', '.dataTables_filter input', function () {--}}
{{--                // Dispatch search to Livewire--}}
{{--                Livewire.dispatch('search_users', [$(this).val()]);--}}

{{--                // Also update the DataTable search (this triggers DataTable's search functionality)--}}
{{--                table.search($(this).val()).draw();--}}

{{--                // Optional: log the search term--}}
{{--                console.log('Search triggered: ' + $(this).val());--}}
{{--            });--}}

{{--            $('a[data-toggle="tab"]').on('shown.bs.tab click', function (e) {--}}
{{--                $($.fn.dataTable.tables(true)).DataTable()--}}
{{--                    .columns.adjust();--}}
{{--            });--}}
{{--        });--}}

{{--    </script>--}}
@endsection
