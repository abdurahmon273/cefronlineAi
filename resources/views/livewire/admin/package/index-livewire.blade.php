<tbody wire:sortable="updateOrder" >
@foreach($packages as $key => $package)
    <tr data-entry-id="{{ $package->id }}" wire:sortable.item="{{ $package->id }}">
        <td></td>
        <td wire:sortable.handle>
            {{ $package->id ?? '' }}
        </td>
        <td>
            {{ number_format($package->price, 0, '.', ',') }}
        </td>
        <td>
            {{ number_format($package->credit_price, 6, '.', ',') }}
        </td>
        <td>
            {{ $package->writing_count ?? '' }}
        </td>
        <td>
            {{ $package->speaking_count ?? '' }}
        </td>
        <td>
            @can('city_edit')
                <a class="btn btn-xs btn-info" href="{{ route('admin.packages.edit', $package->id) }}">
                    {{ trans('global.edit') }}
                </a>
            @endcan

            @can('city_delete')
                <form action="{{ route('admin.packages.destroy', $package->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                </form>
            @endcan
        </td>
    </tr>
@endforeach
</tbody>
