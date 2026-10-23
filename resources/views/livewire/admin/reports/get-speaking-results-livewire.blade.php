<div>
    @if (session()->has('speaking_error'))
        <div class="alert alert-danger">{{ session('speaking_error') }}</div>
    @endif

    <div class="row align-items-end">
        <div class="col-md-6 col-lg-3 mb-2">
            <label class="mb-1">Start date</label>
            <input type="text" id="speaking_start_date" class="form-control" placeholder="Start date" autocomplete="off">
        </div>

        <div class="col-md-6 col-lg-3 mb-2">
            <label class="mb-1">End date</label>
            <input type="text" id="speaking_end_date" class="form-control" placeholder="End date" autocomplete="off">
        </div>

        <div class="col-md-6 col-lg-2 mb-2">
            <label class="mb-1">Bitta faylda nechta</label>
            <select class="form-control" wire:model="count">
                @foreach ($counts as $c)
                    <option value="{{ $c }}">{{ $c }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6 col-lg-2 mb-2">
            <label class="mb-1">Part category</label>
            <select class="form-control" wire:model="category">
                @foreach ($categories as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6 col-lg-2 mb-2">
            <button type="button" class="btn btn-primary w-100"
                    wire:click="download" wire:loading.attr="disabled" wire:target="download">
                <span wire:loading.remove wire:target="download">ZIP yuklab olish</span>
                <span wire:loading wire:target="download">Yuklanmoqda...</span>
            </button>
        </div>
    </div>
</div>
