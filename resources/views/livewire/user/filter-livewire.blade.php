<div>
    <div class="bg-white rounded-3xl p-2">
        <div class="flex  gap-4 items-end justify-between">
{{--            <div class="md:col-span-1 lg:col-span-1">--}}
{{--                <label class="block text-xs font-semibold text-slate-500 uppercase mb-2 ml-1">Test Name</label>--}}
{{--                <div class="relative">--}}
{{--                    <input type="text" wire:model.live="search" placeholder="Search title..."--}}
{{--                        class="w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-orange-500 focus:ring-orange-500 transition-all">--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            <div>--}}
{{--                <label class="block text-xs font-semibold text-slate-500 uppercase mb-2 ml-1">Part</label>--}}
{{--                <select wire:model.live="partType"--}}
{{--                    class="w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-orange-500 focus:ring-orange-500 transition-all">--}}
{{--                    <option value="">All Parts</option>--}}
{{--                    @foreach($this->partOptions as $key => $label)--}}
{{--                        <option value="{{ $key }}">{{ $label }}</option>--}}
{{--                    @endforeach--}}
{{--                </select>--}}
{{--            </div>--}}

{{--            <div>--}}
{{--                <label class="block text-xs font-semibold text-slate-500 uppercase mb-2 ml-1">From Date</label>--}}
{{--                <input type="date" wire:model.live="startDate"--}}
{{--                    class="w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-orange-500 focus:ring-orange-500 transition-all">--}}
{{--            </div>--}}

{{--            <div>--}}
{{--                <label class="block text-xs font-semibold text-slate-500 uppercase mb-2 ml-1">To Date</label>--}}
{{--                <input type="date" wire:model.live="endDate"--}}
{{--                    class="w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-orange-500 focus:ring-orange-500 transition-all">--}}
{{--            </div>--}}

{{--            <div class="flex gap-2">--}}
{{--                <button wire:click="clearFilters"--}}
{{--                    class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold py-2.5 px-4 rounded-2xl transition-all text-sm"--}}
{{--                    id="clearBtn">--}}
{{--                    Clear--}}
{{--                </button>--}}
{{--            </div>--}}

            <div class="flex text-md items-center justify-center gap-2 ml-6 py-2">
              Items Selected: {{count($selectedIds)}}
            </div>
            <div class="flex gap-2 ml-6">
                <button
                    wire:click="confirmDelete"
                    @disabled($this->selectedCount === 0)
                    class="bg-[#e03a3a] hover:bg-red-600 text-white rounded-xl px-4 py-2 text-sm font-bold transition-all shadow-sm
                        @if($this->selectedCount === 0) opacity-50 cursor-not-allowed hover:bg-[#e03a3a] @endif">
                    Delete
                </button>
            </div>
        </div>
    </div>

    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm {{ $showDeleteModal ? '' : 'hidden' }}"
         role="dialog" aria-modal="true" aria-labelledby="delete-title" aria-describedby="delete-desc">
        <div wire:click.away="cancelDelete"
             class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl p-6 space-y-4">
            <h3 id="delete-title" class="text-lg font-semibold text-gray-900">Confirm deletion</h3>
            <p id="delete-desc" class="text-gray-600">Are you sure you want to delete {{ $this->selectedCount }} item(s)? This action cannot be undone.</p>
            <div class="flex justify-end gap-2">
                <button wire:click="cancelDelete" type="button"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition">Cancel</button>
                <button wire:click="deleteSelected" type="button"
                        class="px-4 py-2 bg-[#e03a3a] text-white rounded-xl hover:bg-red-600 transition">Delete</button>
            </div>
        </div>
    </div>
</div>
