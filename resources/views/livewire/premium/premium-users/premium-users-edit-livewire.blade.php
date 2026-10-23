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
            <label>{{ trans('cruds.course.fields.status') }}</label>
            <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" wire:model="plan_id" name="plan_id" id="status">
                <option value  >{{ trans('global.pleaseSelect') }}</option>
                @foreach($plans as $key => $label)
                    <option value="{{ $key }}" {{ $plan_id == (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('plan_id') <span class="text-danger">{{ $message }}</span> @enderror

            <span class="help-block">{{ trans('cruds.course.fields.published_helper') }}</span>
        </div>
        <div class="form-group">
            <label>{{ trans('cruds.course.fields.start_date') }}</label>
            <input type="datetime-local" class="form-control {{ $errors->has('start_date') ? 'is-invalid' : '' }}" wire:model="start_date" >
            @error('start_date') <span class="text-danger">{{ $message }}</span> @enderror

            <span class="help-block">{{ trans('cruds.course.fields.published_helper') }}</span>
        </div>

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



        <div class="form-group">
            <a class="btn btn-default" href="{{ route('admin.premium_users.index') }}">
                {{ trans('global.back_to_list') }}
            </a>
            <button class="btn btn-danger" wire:click="saqlash" type="button">
                {{ trans('global.save') }}
            </button>
        </div>
    </form>
</div>
