<tbody wire:sortable="updateOrder">
@foreach($writingExercises as $exercise)
    <tr data-entry-id="{{ $exercise->id }}" wire:sortable.item="{{ $exercise->id }}">
        <td></td>
        <td wire:sortable.handle>{{ $exercise->id ?? '' }}</td>
        <td>
            <a style="color: black" href="">
                {{ $exercise->name ?? '' }}
            </a>
        </td>
        <td>{{ $exercise->task_one?->time_limit }} min</td>
        <td>{{ $exercise->task_one?->word_count }}</td>
        <td>{{ \App\Models\Lesson::STATUS[$exercise->status] ?? 'free' }}</td>
        <td>
            <a class="btn btn-xs btn-info"
               href="{{ route('admin.writings_exercise.task_one.edit', $exercise->id) }}">
                {{ trans('global.edit') }}
            </a>
            <form action="{{ route('admin.writings.exercise.destroy', $exercise->id) }}" method="POST"
                  onsubmit="return confirm('{{ trans('global.readingYouSure') }}');"
                  style="display: inline-block;">
                @csrf
                @method('DELETE')
                <input type="submit" class="btn btn-xs btn-danger"
                       value="{{ trans('global.delete') }}">
            </form>
        </td>
    </tr>
@endforeach

<!-- Pagination Links -->
<div class="mt-4">
    {{ $writingExercises->links() }}
</div>

</tbody>
