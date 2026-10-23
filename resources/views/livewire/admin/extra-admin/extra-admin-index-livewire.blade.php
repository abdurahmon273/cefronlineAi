<div class="table-responsive">
    <input type="text" class="form-control mb-2" placeholder="Search Users..." wire:model.live="search" />

    <table class=" table table-bordered table-striped table-hover datatable datatable-User">
        <thead>
        <tr>
            <th width="10">

            </th>
            <th>
                {{ trans('cruds.user.fields.id') }}
            </th>
            <th>
                {{ trans('cruds.user.fields.name') }}
            </th>
            <th>
                {{ trans('cruds.user.fields.mobil_number') }}
            </th>
            <th>
                {{ trans('cruds.user.fields.roles') }}
            </th>
            <th>
                Qoshilgan vaqti
            </th>
            <th>
                &nbsp;
            </th>
        </tr>
        </thead>

        <tbody>

        @php
            $user_edit=Gate::check("user_edit");
            $user_delete=Gate::check("user_delete");
            $user_show=Gate::check("user_show");
        @endphp

        @foreach($users as $key => $user)
            <tr data-entry-id="{{ $user->id }}">
                <td>

                </td>
                <td>
                    {{ $user->id ?? '' }}
                </td>
                <td>

                        {{ $user->name ?? '' }}
                </td>
                <td>

                        {{ $user->mobil_number ?? '' }}
                </td>

                <td>
                    @foreach($user->roles as $key => $item)
                        <span class="badge badge-info">{{ $item->title }}</span>
                    @endforeach
                </td>
                <td>
                    {{$user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('Y-m-d H:i') : null }}
                </td>


                <td>
                    @if($user_edit)
                        <a class="btn btn-xs btn-info" href="{{ route('admin.extra_admins.edit', $user->id) }}">
                            {{ trans('global.edit') }}
                        </a>
                    @endif

                    @if($user_delete)
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                              onsubmit="return confirm('{{ trans('global.areYouSure') }}');"
                              style="display: inline-block;">
                            <input type="hidden" name="_method" value="DELETE">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="submit" class="btn btn-xs btn-danger"
                                   value="{{ trans('global.delete') }}">
                        </form>
                    @endif

                </td>

            </tr>
        @endforeach
        </tbody>


    </table>
    <div class="d-flex justify-content-center">
        {{ $users->links() }}
    </div>
</div>
