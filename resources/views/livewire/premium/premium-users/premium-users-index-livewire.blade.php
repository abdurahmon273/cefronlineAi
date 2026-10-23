    <div class="table-responsive">
        <div class="d-flex justify-content-start mb-2" >
            <button class="btn btn-secondary border " wire:click="toggleFilters" >Filter</button>
        </div>
        <div class="row mb-3  p-2 border {{ $showFilters ? '' : 'd-none' }} " id="filter_div">
            <div class="col-6">
                <input type="text" class="form-control mb-2 " placeholder="Search Users..." wire:model.live="search" />
            </div>
            <div class="col-3">
                <select class="form-control" wire:model.live="type">
                    <option value="">All</option>
                    <option value="0">App</option>
                    <option value="2">Web </option>
                    <option value="1">By Admin </option>
                    @foreach($extra_admins as $id=>$name )
                        <option value="{{$id}}">{{$name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-3">
                <select class="form-control" wire:model.live="plan_id">
                    <option value="">All</option>
                    @foreach($plans as $id=>$name )
                        <option value="{{$id}}">{{$name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-3">
                <input type="text" id="start_date" class="form-control" placeholder="Start Date" wire:model.lazy="start_date">
            </div>
            <div class="col-3">
                <input type="text" id="end_date" class="form-control" placeholder="End Date" wire:model.lazy="end_date">
            </div>

            <div class="col-2">
                <select class="form-control" wire:model.live="paginate">
                    <option value="50">50 paginate</option>
                    <option value="100">100 paginate</option>
                    <option value="200">200 paginate</option>
                    <option value="300">300 paginate</option>
                </select>
            </div>
            <div class="col-4 d-flex gap-2">
                <button wire:click="export" wire:loading.attr="disabled" wire:target="export" class="btn btn-sm btn-outline-success">
                    <span wire:loading.remove wire:target="export">Excel Export</span>
                    <span wire:loading wire:target="export">Yuklanmoqda...</span>
                </button>

                <button wire:click="metaExport" wire:loading.attr="disabled" wire:target="metaExport" class=" ml-3 btn btn-sm btn-outline-primary">
                    <span wire:loading.remove wire:target="metaExport">Meta Excel Export</span>
                    <span wire:loading wire:target="metaExport">Yuklanmoqda...</span>
                </button>

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
                <th> {{ trans('cruds.premium_users.fields.name') }} </th>
                <th> {{ trans('cruds.premium_users.fields.phone_number') }} </th>
                <th> {{ trans('cruds.premium_users.fields.start_date') }} </th>
                <th> {{ trans('cruds.premium_users.fields.end_date') }} </th>
                <th> {{ trans('cruds.premium_users.fields.promoCode') }} </th>
                <th> {{ trans('cruds.premium_users.fields.plan') }} </th>
                <th> {{ trans('cruds.premium_users.fields.type') }} </th>
                <th style="width: 14%">&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            @foreach($premium_users as $key=>$premium_to_user)
                <tr data-entry-id="{{ $premium_to_user->id }}">
                    <td>{{$key+1}}</td>
                    <td> {{ $premium_to_user->id ?? '' }} </td>
                    <td> {{ optional($premium_to_user->user)->name ?? '—' }} {{ optional($premium_to_user->user)->surname ?? '' }} </td>
                    <td> {{ optional($premium_to_user->user)->mobil_number ?? '' }} </td>
                    <td> {{ $premium_to_user->start_time ? \Carbon\Carbon::parse($premium_to_user->start_time)->format('j M Y, H:i') : '' }} </td>
                    <td> {{ $premium_to_user->end_time ? \Carbon\Carbon::parse($premium_to_user->end_time)->format('j M Y, H:i') : '' }} </td>
                    <td>
                        <div class="w-100 h-100 d-flex justify-content-center align-items-center flex-column gap-2">
                            @if($premium_to_user->promoCode )
                            <span class="badge p-1 badge-info " style="font-size: 14px">{{ $premium_to_user->promoCode->code }}</span>
                            <span class="badge mt-2 p-1 badge-success">{{ $premium_to_user->promoCode->discount }}%</span>
                            @endif
                        </div>
                    </td> <td>
                        <button class="btn btn-outline-primary">
                            {{ $premium_to_user->plan->name }}
                        </button>
                    </td>
                    <td>
                        <button class="btn {{ ( $premium_to_user->type=='0' || $premium_to_user->type=='2' ) ? 'btn-outline-info' : 'btn-outline-success' }}">
                            {{ $premium_to_user->type=='0'  ? 'App' : ( $premium_to_user->type=='2' ? 'Web' : ( $premium_to_user->admin ? "By ".$premium_to_user->admin->name : 'By Admin') )}}
                        </button>
                    </td>
                    <td>
                        @can('premium_users_edit')
                            <a class="btn btn-xs btn-info" href="{{ route('admin.premium_users.edit', $premium_to_user->id) }}">
                                {{ trans('global.edit') }}
                            </a>
                        @endcan
                        @can('premium_users_delete')
                            <form action="{{ route('admin.premium_users.destroy', $premium_to_user->id) }}" method="POST"
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
            {{ $premium_users->links() }}
        </div>



    </div>
