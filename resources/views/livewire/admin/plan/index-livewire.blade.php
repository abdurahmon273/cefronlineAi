<tbody wire:sortable="updateOrder" >
@foreach($plans as $key => $plan)
    <tr data-entry-id="{{ $plan->id }}" wire:sortable.item="{{ $plan->id }}">
        <td></td>
        <td>
            {{ $plan->id ?? '' }}
        </td>
        <td>
            {{ $plan->name ?? '' }}
        </td>
        <td>
            {{ number_format($plan->price, 0, '.', ',') }}
        </td>
        <td>
            {{ $plan->day ?? '' }}
        </td>
        <td>
            {{ $plan->credit_price ?? '' }}
        </td>
        <td>
            @can('city_edit')
                <a class="btn btn-xs btn-info" href="{{ route('admin.plans.edit', $plan->id) }}">
                    {{ trans('global.edit') }}
                </a>
            @endcan

            @can('city_delete')
                <form action="{{ route('admin.plans.destroy', $plan->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                </form>
            @endcan
        </td>
    </tr>
@endforeach
</tbody>
