<div class="card-body">
    <form  enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label>{{ trans('cruds.premium_users.fields.promoCode') }}</label>
            <input type="text" class="form-control {{ $errors->has('promoCode') ? 'is-invalid' : '' }}" wire:model="promoCode" >
            @error('promoCode') <span class="text-danger">{{ $message }}</span> @enderror

            <span class="help-block">{{ trans('cruds.course.fields.published_helper') }}</span>
        </div>

        <div class="form-group">
            <label>{{ trans('cruds.package_users.title_singular') }}</label>
            <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" wire:model="package_id" name="package_id" id="status">
                <option value  >{{ trans('global.pleaseSelect') }}</option>
                @foreach($packages as $key => $label)
                    <option value="{{ $label['id'] }}" {{ $package_id == (string) $label['id'] ? 'selected' : '' }}>{{ number_format($label['price'],0) }} uzs | writing count: {{$label['writing_count']}} | speaking count {{$label['speaking_count']}}</option>
                @endforeach
            </select>
            @error('package_id') <span class="text-danger">{{ $message }}</span> @enderror

            <span class="help-block">{{ trans('cruds.course.fields.published_helper') }}</span>
        </div>

        <livewire:premium.premium-users.search-users-input-livewire></livewire:premium.premium-users.search-users-input-livewire>


        <div class="form-group">
            <a class="btn btn-default" href="{{ route('admin.premium_users.index') }}">
                {{ trans('global.back_to_list') }}
            </a>


            @error('user_id')
            <div class="alert alert-danger mt-2">
                <i class="fas fa-exclamation-circle"></i> {{ $message }}
            </div>
            @enderror

            <button class="btn btn-danger"
                    wire:click="saqlash"
                    wire:loading.attr="disabled"
                    @if($isSaving) disabled @endif
                    type="button">
                <span wire:loading.remove wire:target="saqlash">
                    @if($isSaving)
                        <i class="fas fa-spinner fa-spin"></i> Saqlanmoqda...
                    @else
                        {{ trans('global.save') }}
                    @endif
                </span>
                <span wire:loading wire:target="saqlash">
                    <i class="fas fa-spinner fa-spin"></i> Saqlanmoqda...
                </span>
            </button>

        </div>
    </form>
</div>
