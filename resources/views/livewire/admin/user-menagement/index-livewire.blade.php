<div>
    @php
        $user_edit=Gate::check("user_edit");
        $user_delete=Gate::check("user_delete");
        $user_show=Gate::check("user_show");
        $user_login=Gate::check("user_login");
    @endphp

    <div class="row mb-3 align-items-center">
        <div class="col-8">
            <input type="text" class="form-control" placeholder="Search Users..." wire:model.live="search" />
        </div>
        <div class="col-3">
            <button wire:click="exportExcel" class="btn btn-success w-100" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="exportExcel">
                    <i class="fa fa-file-excel-o mr-1"></i> Excel Export
                </span>
                <span wire:loading wire:target="exportExcel">
                    <span class="spinner-border spinner-border-sm mr-1" role="status"></span>
                    Yuklanmoqda...
                </span>
            </button>
        </div>
    </div>

<div class="table-responsive">

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
                {{ trans('cruds.user.fields.surname') }}
            </th>
            <th>
                {{ trans('cruds.user.fields.roles') }}
            </th>

            <th>
                Qoshilgan vaqti
            </th>
            @if($user_login)
                <th>
                    login
                </th>
            @endif
{{--            <th>--}}
{{--                {{ trans('cruds.user.fields.birth_date') }}--}}
{{--            </th>--}}
{{--            <th>--}}
{{--                {{ trans('cruds.user.fields.gender') }}--}}
{{--            </th>--}}
{{--            <th>--}}
{{--                {{ trans('cruds.user.fields.city') }}--}}
{{--            </th>--}}
{{--            <th>--}}
{{--                {{ trans('cruds.region.title') }}--}}
{{--            </th>--}}
            <th>
                &nbsp;
            </th>
        </tr>
        </thead>

        <tbody>



@foreach($users as $key => $user)
    <tr data-entry-id="{{ $user->id }}">
        <td>

        </td>
        <td>
            {{ $user->id ?? '' }}
        </td>
        <td>
            @if($user_show)
                <a class="text-info"
                   href="{{ route('admin.users.show', $user->id) }}">{{ $user->name ?? '' }}</a>
            @else
                {{ $user->name ?? '' }}
            @endif
        </td>
        <td>
            {{ $user->surname ?? '' }}
        </td>
        <td>
            @foreach($user->roles as $key => $item)
                <span class="badge badge-info">{{ $item->title }}</span>
            @endforeach
        </td>
        <td>
            {{$user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('Y-m-d H:i') : null }}
        </td>
{{--        <td>--}}
{{--            {{ $user->birth_date ?? '' }}--}}
{{--        </td>--}}
{{--        <td>--}}
{{--            {{ App\Models\User::GENDER_SELECT[$user->gender] ?? '' }}--}}
{{--        </td>--}}
{{--        <td>--}}
{{--            {{ $user->city->name ?? '' }}--}}
{{--        </td>--}}
        @if($user_login)
        <td>
            <a class="btn btn-xs btn-info"
               href="{{ route('admin.redirect.login', ['id' => $user->id]) }}"
               target="_blank">
                {{ trans('global.login') }}
            </a>
        </td>
        @endif
        <td>
            @if($user_edit)
                <a class="btn btn-xs btn-info" href="{{ route('admin.users.edit', $user->id) }}">
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
</div>
