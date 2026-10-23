<div>
    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row align-items-end">
        <div class="col-md-3 mb-2">
            <label class="mb-1">Start date</label>
            <input type="text" id="start_date" class="form-control" placeholder="Start date" autocomplete="off">
        </div>

        <div class="col-md-3 mb-2">
            <label class="mb-1">End date</label>
            <input type="text" id="end_date" class="form-control" placeholder="End date" autocomplete="off">
        </div>

        <div class="col-md-3 mb-2">
            <label class="mb-1">Har bir faylda nechta user</label>
            <select class="form-control" wire:model="count">
                @foreach ($counts as $c)
                    <option value="{{ $c }}">{{ $c }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3 mb-2">
            <button type="button" class="btn btn-primary w-100"
                    wire:click="send" wire:loading.attr="disabled" wire:target="send">
                <span wire:loading.remove wire:target="send">Telegram'ga yuborish</span>
                <span wire:loading wire:target="send">Yuborilmoqda...</span>
            </button>
        </div>
    </div>
</div>
