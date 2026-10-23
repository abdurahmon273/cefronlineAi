<?php

namespace App\Livewire\User\Results;

use App\Enums\CefrTypeEnum;
use App\Models\FullResult;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class FullExamIndexLivewire extends Component
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

        $this->dispatch('selectionsUpdated', selectedIds: $this->selected)
            ->to('user.filter-livewire');
    }

    public function updatedSelected()
    {
        $this->syncSelectAllState();

        $this->dispatch('selectionsUpdated', selectedIds: $this->selected)
            ->to('user.filter-livewire');
    }

    #[On('resetSelection')]
    public function resetSelection()
    {
        $this->selected = [];
        $this->selectAll = false;

        $this->dispatch('selectionsUpdated', selectedIds: [])
            ->to('user.filter-livewire');
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
        $results = FullResult::query()
            ->with(['exercise'])
            ->where('user_id', auth()->id())
            ->where('status', 1)
            ->whereHas('exercise', function ($query) {
                $query->when($this->search, function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%');
                })->when($this->partType, function ($q) {
                    $q->where('part_category', $this->partType);
                });
            })
            ->when($this->startDate, function ($q) {
                $q->whereDate('created_at', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($q) {
                $q->whereDate('created_at', '<=', $this->endDate);
            })
            ->orderByDesc('date')
            ->paginate(4);

        $results->getCollection()->transform(function ($result) {
            $result->listening_ball = 0;
            $result->reading_ball = 0;
            $result->writing_ball = 0;
            $result->speaking_ball = 0;
            $count=0;

            if ($result->listening_result){
                $result->listening_ball= CefrTypeEnum::LISTENING_BALL[$result->listening_result->correct_answers] ;
                $count++;
            }
            if ($result->reading_result){
                $result->listening_ball= CefrTypeEnum::READING_BALL[$result->reading_result->correct_answers] ;
                $count++;
            }

            if ($result->speaking_result){
                $key = rtrim(rtrim(number_format((float) $result->speaking_result->score, 1, '.', ''), '0'), '.');

                $result->speaking_ball = CefrTypeEnum::SPEAKING_BALL[4][$key] ?? 0;
                $count++;
            }

            if ($result->writing_result){
                $key = rtrim(rtrim(number_format((float) $result->writing_result->score, 1, '.', ''), '0'), '.');
                $result->writing_ball= CefrTypeEnum::WRITING_BALL[3][$key] ;
                $count++;
            }

            $result->overall = number_format (($result->listening_ball+$result->reading_ball+$result->speaking_ball) / ($count > 0 ? $count : 1),0);

            return $result;
        });

        return $results;
    }

    #[On('deleteItems')]
    public function deleteSelected($ids = [], $section = null)
    {
        if ($section !== null && $section !== 'full') {
            return;
        }

        $idsToDelete = array_filter(
            array_map('intval', !empty($ids) ? $ids : $this->selected)
        );

        if (empty($idsToDelete)) {
            session()->flash('error', 'No items selected');
            return;
        }

        try {
            FullResult::whereIn('id', $idsToDelete)
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
                ->map(fn ($id) => (string) $id)
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
                ->map(fn ($id) => (string) $id)
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getAllResultsQuery()
    {
        return FullResult::query()
            ->where('user_id', auth()->id())
            ->where('status', 1)
            ->whereHas('exercise', function ($query) {
                $query->when($this->search, function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%');
                })->when($this->partType, function ($q) {
                    $q->where('part_category', $this->partType);
                });
            })
            ->when($this->startDate, function ($q) {
                $q->whereDate('created_at', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($q) {
                $q->whereDate('created_at', '<=', $this->endDate);
            });
    }

    private function syncSelectAllState(): void
    {
        $pageIds = $this->getPageIds();
        $this->selectAll = !empty($pageIds) && empty(array_diff($pageIds, $this->selected ?? []));
    }

    public function render()
    {
        return view('livewire.user.results.full-exam-index-livewire', [
            'results' => $this->results,
        ]);
    }
}
