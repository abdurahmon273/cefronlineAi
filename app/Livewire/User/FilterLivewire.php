<?php

namespace App\Livewire\User;

use Livewire\Component;

class FilterLivewire extends Component
{
    public $section = '';
    public $search = '';
    public $category = '';
    public $partType = '';
    public $startDate = '';
    public $endDate = '';
    public $selectedIds = [];
    public $showDeleteModal = false;

    public function updated($property, $value)
    {
        $this->dispatch('filterUpdated', [
            'search' => $this->search,
            'category' => $this->category,
            'partType' => $this->partType,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }

    public function clearFilters()
    {
        $this->reset(['search', 'category', 'partType', 'startDate', 'endDate']);
        $this->selectedIds = [];
        $this->dispatch('filterUpdated', [
            'search' => '',
            'category' => '',
            'partType' => '',
            'startDate' => '',
            'endDate' => '',
        ]);
        $this->dispatch('resetSelection');
    }

    public function toggleCheckbox($id)
    {
        if (in_array($id, $this->selectedIds)) {
            $this->selectedIds = array_filter($this->selectedIds, fn($item) => $item !== $id);
        } else {
            $this->selectedIds[] = $id;
        }
    }

    #[\Livewire\Attributes\On('selectionsUpdated')]
    public function updateSelections($selectedIds = [])
    {
        $this->selectedIds = $selectedIds;
    }

    #[\Livewire\Attributes\On('tabChanged')]
    public function onTabChanged()
    {
        $this->clearFilters();
    }

    public function deleteSelected()
    {
        $this->showDeleteModal = false;

        if (empty($this->selectedIds)) {
            session()->flash('error', 'No items selected');
            return;
        }

        $this->dispatch('deleteItems', ids: $this->selectedIds, section: $this->section);
        $this->selectedIds = [];
    }

    public function confirmDelete()
    {
        if (empty($this->selectedIds)) {
            return;
        }

        $this->showDeleteModal = true;
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
    }

    #[\Livewire\Attributes\Computed]
    public function selectedCount()
    {
        return count($this->selectedIds);
    }

    #[\Livewire\Attributes\Computed]
    public function partOptions()
    {
        $sectionMap = [
            'listening' => 0,
            'reading' => 1,
            'writing' => 2,
            'speaking' => 3,
            'full_mock' => 4,
        ];

        $index = $sectionMap[$this->section] ?? 0;
        return \App\Enums\CefrTypeEnum::PART_CATEGORIES[$index] ?? [];
    }

    #[\Livewire\Attributes\Computed]
    public function showSelectionBar()
    {
        return $this->selectedCount > 0;
    }

    public function render()
    {
        return view('livewire.user.filter-livewire');
    }
}
