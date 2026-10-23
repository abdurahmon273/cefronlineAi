<div class="table-responsive">
    <div class="d-flex justify-content-start mb-2" >
        <button class="btn btn-secondary border " wire:click="toggleFilters" >Filter</button>
    </div>
    <div class="row mb-3  p-2 border {{ $showFilters ? '' : 'd-none' }} " id="filter_div">
        <div class="col-6">
            <input type="text" class="form-control mb-2 " placeholder="Search Users..." wire:model.live="search" />
        </div>
        <div class="col-2">
            <select class="form-control" wire:model.live="type">
                <option value="">All</option>
                <option value="0">App</option>
                <option value="1">By Admin </option>
                <option value="2">By Plan </option>
                @foreach($extra_admins as $id=>$name )
                    <option value="{{$id}}">{{$name}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-2">
            <select class="form-control" wire:model.live="paginate">
                <option value="50">50 paginate</option>
                <option value="100">100 paginate</option>
                <option value="200">200 paginate</option>
                <option value="300">300 paginate</option>
            </select>
        </div>
        <div class="col-2">
            <button wire:click="export" wire:loading.attr="disabled" class="btn btn-outline-success w-75">
                <span wire:loading.remove>Excel Export</span>
                <span wire:loading>Yuklanmoqda...</span>
            </button>

        </div>
        <div class="col-6">
            <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" wire:model.live="package_id" name="package_id" id="status">
                <option value=""  >select Package</option>
                @foreach($packages as $key => $label)
                    <option value="{{ $label['id'] }}" {{ $package_id == (string) $label['id'] ? 'selected' : '' }}>{{ number_format($label['price'],0) }} uzs | writing count: {{$label['writing_count']}} | speaking count {{$label['speaking_count']}}</option>
                @endforeach
            </select>
        </div>

        <div class="col-3">
            <input type="text" id="start_date" class="form-control" placeholder="Start Date" wire:model.lazy="start_date">
        </div>
        <div class="col-3">
            <input type="text" id="end_date" class="form-control" placeholder="End Date" wire:model.lazy="end_date">
        </div>


    </div>
    <div class="d-flex justify-content-start">

        <h5>Summa: {{ number_format(($filteredSum),'0','.',' ') }} so`m</h5>
    </div>


    <table class=" table table-bordered table-striped table-hover datatable datatable-City">
        <thead>
        <tr>
            <th width="10"></th>
            <th style="width: 4%"> {{ trans('cruds.city.fields.id') }} </th>
            <th> {{ trans('cruds.package_users.fields.name') }} </th>
            <th> {{ trans('cruds.package_users.fields.phone_number') }} </th>
            <th> {{ trans('cruds.package_users.fields.price') }} </th>
            <th> {{ trans('cruds.package_users.fields.credit_price') }} </th>
            <th> {{ trans('cruds.package.fields.writing_count') }} </th>
            <th> {{ trans('cruds.package.fields.speaking_count') }} </th>
            <th> {{ trans('cruds.premium_users.fields.promoCode') }} </th>
            <th> {{ trans('cruds.package_users.fields.type') }} </th>
            <th style="width: 14%">&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        @foreach($package_users as $key=>$package_to_user)
            <tr data-entry-id="{{ $package_to_user->id }}">
                <td>{{$key+1}}</td>
                <td> {{ $package_to_user->id ?? '' }} </td>
                <td> {{ optional($package_to_user->user)->name ?? '—' }} {{ optional($package_to_user->user)->surname ?? '' }} </td>
                <td> {{ optional($package_to_user->user)->mobil_number ?? '' }} </td>
                <td>
                    @if($package_to_user->discount > 0)
                        <span>
                             {{ number_format(($package_to_user->price / 100 * (100 - $package_to_user->discount)), 0) }}
                         </span>

                        <span style="text-decoration: line-through; color:#999;">
                            {{ number_format($package_to_user->price, 0) }}
                        </span>
                    @else
                        <span>
                            {{ number_format($package_to_user->price, 0) }}
                        </span>
                    @endif

                </td>
                <td>
                        {{ $package_to_user->value }}
                </td>
                <td>
                        {{ $package_to_user->package?->writing_count }}
                </td>
                <td>
                        {{ $package_to_user->package?->speaking_count }}
                </td>
                <td>
                    <div class="w-100 h-100 d-flex justify-content-center align-items-center flex-column gap-2">
                        @if($package_to_user->promoCode )
                            <span class="badge p-1 badge-info " style="font-size: 14px">{{ $package_to_user->promoCode->code }}</span>
                            <span class="badge mt-2 p-1 badge-success">{{ $package_to_user->discount }}%</span>
                        @endif
                    </div>
                </td>
                <td>
                    <button class="btn {{ $package_to_user->type=='0' ? 'btn-outline-info' : 'btn-outline-success' }}">
                        {{ $package_to_user->type=='0' ? 'App' : (  $package_to_user->admin ? "By ".$package_to_user->admin->name : 'By Admin' )}}
                    </button>
                </td>
                <td>
                    @can('premium_users_edit')
                        <a class="btn btn-xs btn-info" href="{{ route('admin.package_users.edit', $package_to_user->id) }}">
                            {{ trans('global.edit') }}
                        </a>
                    @endcan
                    @can('premium_users_delete')
                        <form action="{{ route('admin.package_users.destroy', $package_to_user->id) }}" method="POST"
                              onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                        </form>
                    @endcan
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <!-- Livewire pagination qo‘llash -->
    <div class="">
        {{ $package_users->links() }}
    </div>



</div>
