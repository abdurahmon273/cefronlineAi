<tbody wire:sortable="updateOrder">
@foreach($stepPages as $stepPage)
    <tr data-entry-id="{{ $stepPage->id }}" wire:sortable.item="{{ $stepPage->id }}">
        <!-- Drag handle -->
        <td width="32" class="text-muted" >

        </td>

        <!-- ID -->
        <td wire:sortable.handle> {{ $stepPage->id }}</td>

        <!-- Title (+ order info) -->
        <td>
            <a style="color:black"
               href="{{ route('admin.stepPages.edit', $stepPage->id) }}">
                {{ $stepPage->title }}
            </a>

        </td>

        <!-- Short description -->
        <td>
            {{ \Illuminate\Support\Str::limit(strip_tags($stepPage->description), 120) }}
        </td>

        <!-- Image thumbnail -->
        <td class="text-center">
            @if($stepPage->image)
                <a href="{{ asset('storage/'.$stepPage->image) }}" target="_blank" rel="noopener">
                    <img src="{{ asset('storage/'.$stepPage->image) }}"
                         alt="{{ $stepPage->title }}"
                         class="img-thumbnail"
                         style="max-height:48px; max-width:80px; object-fit:cover;">
                </a>
            @else
                <span class="text-muted">{{ __('No image') }}</span>
            @endif
        </td>

        <!-- Actions -->
        <td>


            @can('stepPage_edit')
                <a class="btn btn-xs btn-info"
                   href="{{ route('admin.stepPages.edit', $stepPage->id) }}">
                    {{ trans('global.edit') }}
                </a>
            @endcan

            @can('stepPage_delete')
                <form action="{{ route('admin.stepPages.destroy', $stepPage->id) }}"
                      method="POST"
                      onsubmit="return confirm('{{ trans('global.areYouSure') }}');"
                      style="display:inline-block;">
                    @method('DELETE')
                    @csrf
                    <input type="submit" class="btn btn-xs btn-danger"
                           value="{{ trans('global.delete') }}">
                </form>
            @endcan
        </td>
    </tr>
@endforeach
</tbody>
