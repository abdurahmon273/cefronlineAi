<div class="form-group">
    <label class="required" for="roles">{{ trans('cruds.premium_users.fields.user') }}</label>
    <div class="d-flex justify-content-between gap-1">
        <input autocomplete="off" class="form-control w-50 mb-1" type="text" wire:model.live.debounce.400ms="search" placeholder="search users by name or phone number">
        <div class="width35 border border-2 d-flex p-2 justify-content-between">
            @if($chosen_user)
                <div class="">{{$chosen_user['name']}} </div>
                <div class="">{{$chosen_user['phone_number']}}</div>
            @endif
        </div>
    </div>

    <div class="">

        @foreach($founded_users as $user)

            <button wire:click="choose_user( {{$user['id']}} )" type="button" class="w-50 border border-2 d-flex p-2 justify-content-between">
                <div class="">{{$user['name']}} </div>
                <div class="">{{$user['phone_number']}}</div>
            </button>

        @endforeach

        <div class="w-50 border border-2 d-flex p-2 justify-content-center bg-secondary d-none {{count($founded_users)==0 ? 'DisplayNone' : ''}} ">
            <div class="">Bunday foydalanuvchi Topilmadi </div>
        </div>

    </div>

    @error('user_id') <span class="text-danger">{{ $message }}</span> @enderror

</div>
