<?php

namespace App\Livewire\User\Results;

use App\Models\SpeakingResult;
use App\Models\WritingResult;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;

class SpeakingIndexLivewire extends Component
{
    use WithPagination;

    public $search = '';
    public $partType = '';
    public $startDate = '';
    public $endDate = '';
    public $selected = [];
    public $selectAll = false;

    public function updatedPage($page)
    {
        $this->syncSelectAllState();
    }

    public function updatedSelectAll($value)
    {
        $pageIds = $this->getPageIds();

        if ($value) {
            $this->selected = array_values(array_unique(array_merge($this->selected ?? [], $pageIds ?? [])));
        } else {
            $this->selected = array_values(array_diff($this->selected ?? [], $pageIds ?? []));
        }
        $this->dispatch('selectionsUpdated', selectedIds: $this->selected)->to('user.filter-livewire');
    }

    public function updatedSelected()
    {
        $this->syncSelectAllState();
        $this->dispatch('selectionsUpdated', selectedIds: $this->selected)->to('user.filter-livewire');
    }

    #[On('resetSelection')]
    public function resetSelection()
    {
        $this->selected = [];
        $this->selectAll = false;
        $this->dispatch('selectionsUpdated', selectedIds: [])->to('user.filter-livewire');
    }

    #[On('filterUpdated')]
    public function applyFilter($data = [])
    {
        $this->search = $data['search'] ?? '';
        $this->partType = $data['partType'] ?? '';
        $this->startDate = $data['startDate'] ?? '';
        $this->endDate = $data['endDate'] ?? '';
        $this->resetPage();
        $this->resetSelection();
    }

    #[Computed]
    public function results()
    {
        return SpeakingResult::query()
            ->with(['exercise'])
            ->where('user_id', auth()->id())
            ->whereDoesntHave('full_results')
            ->whereHas('exercise', function ($query) {
                $query->when($this->search, fn($q) => $q->where('title', 'like', '%' . $this->search . '%'))
                    ->when($this->partType, fn($q) => $q->where('part_category', $this->partType));
            })
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
            ->orderBy('created_at', 'desc')
            ->paginate(4);
    }

    #[On('deleteItems')]
    public function deleteSelected($ids = [], $section = null)
    {
        if ($section !== null && $section !== 'speaking') {
            return;
        }
        $idsToDelete = array_filter(array_map('intval', !empty($ids) ? $ids : $this->selected));

        if (empty($idsToDelete)) {
            session()->flash('error', 'No items selected');
            return;
        }

        try {
            SpeakingResult::whereIn('id', $idsToDelete)
                ->where('user_id', auth()->id())
                ->delete();

            $this->dispatch('global_alert', 1, count($idsToDelete) . ' items deleted successfully');
            $this->resetSelection();
        } catch (\Exception $e) {

            $this->dispatch('global_alert', 0,  'Error deleting items: ' . $e->getMessage());
        }
    }


    private function getPageIds(): array
    {
        try {
            if (!is_object($this->results) || !method_exists($this->results, 'getCollection')) {
                return [];
            }
            return $this->results->getCollection()
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getAllResultIds(): array
    {
        try {
            return $this->getAllResultsQuery()
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getAllResultsQuery()
    {
        return SpeakingResult::query()
            ->where('user_id', auth()->id())
            ->whereDoesntHave('full_results')
            ->whereHas('exercise', function ($query) {
                $query->when($this->search, fn($q) => $q->where('title', 'like', '%' . $this->search . '%'))
                    ->when($this->partType, fn($q) => $q->where('part_category', $this->partType));
            })
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate));
    }

    private function syncSelectAllState(): void
    {
        $pageIds = $this->getPageIds();
        $this->selectAll = !empty($pageIds) && empty(array_diff($pageIds, $this->selected ?? []));
    }

    public function render()
    {

        return view('livewire.user.results.speaking-index-livewire',[
        'results' => $this->results,
        ]);
    }
}
