<div class="table-responsive">
    <div class="d-flex justify-content-start mb-2" >
        <button class="btn btn-secondary border " wire:click="toggleFilters" >Filter</button>
    </div>
    <div class="row mb-3  p-2 border {{ $showFilters ? '' : 'd-none' }} " id="filter_div">
        <div class="col-6">
            <input type="text" class="form-control mb-2 " placeholder="Search PromoCode..." wire:model.live="search" />
        </div>
        <div class="col-3" wire:ignore>
            <select class="form-control select2" id="packages" multiple>
                @foreach($packages as $id => $package)
                    <option value="{{ $id }}" {{ in_array($id, $package_ids ?? []) ? 'selected' : '' }}>
                        {{ $package }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-3" wire:ignore>
            <select class="form-control select2" id="plans" multiple>
                @foreach($plans as $id => $plan)
                    <option value="{{ $id }}" {{ in_array($id, $plan_ids ?? []) ? 'selected' : '' }}>
                        {{ $plan }}
                    </option>
                @endforeach
            </select>
        </div>


        <div class="col-2">
            <input type="text" id="start_date" class="form-control" placeholder="Start Date" wire:model.lazy="start_date">
        </div>
        <div class="col-2">
            <input type="text" id="end_date" class="form-control" placeholder="End Date" wire:model.lazy="end_date">
        </div>

        <div class="col-2">
            <select class="form-control" wire:model.live="type">
                <option value="">All</option>
                @foreach(\App\Models\Promocode::TYPES as $key=>$type )
                    <option value="{{$key}}">{{$type}}</option>
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
        <div class="col-4">
            <button wire:click="export" wire:loading.attr="disabled" class="btn btn-outline-success w-75">
                <span wire:loading.remove>Excel Export</span>
                <span wire:loading>Yuklanmoqda...</span>
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
            <th> {{ trans('cruds.promoCode.fields.code') }} </th>
            <th> {{ trans('cruds.promoCode.fields.type') }} </th>
            <th> {{ trans('cruds.promoCode.fields.start_date') }} </th>
            <th> {{ trans('cruds.promoCode.fields.end_date') }} </th>
            <th> {{ trans('cruds.promoCode.fields.discount') }} </th>
            <th> {{ trans('cruds.promoCode.fields.plans') }} </th>
            <th> {{ trans('cruds.promoCode.fields.packages') }} </th>
            <th> {{ trans('cruds.promoCode.fields.status') }} </th>
            <th style="width: 15%">&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        @foreach($premium_users as $key=>$promoCode)
            <tr data-entry-id="{{ $promoCode->id }}">
                <td></td>
                <td> {{ $promoCode->id ?? '' }} </td>
                <td>
                <p>{{$promoCode->code}}</p>
                </td>
                <td>
                    {{\App\Models\Promocode::TYPES[$promoCode->type]}}
                </td>
                <td> {{ $promoCode->start_date ? \Carbon\Carbon::parse($promoCode->start_date)->format('j M Y') : ''}} </td>
                <td> {{ $promoCode->end_date ? \Carbon\Carbon::parse($promoCode->end_date)->format('j M Y') : ''}} </td>
                <td>
                    <div class="w-100 h-100 d-flex justify-content-center align-items-center">
                        <span class="badge p-1 badge-success">{{ $promoCode->discount }}%</span>
                    </div>
                </td>
                <td>
                    @foreach($promoCode->plans as $id => $plan)
                        <span class="badge badge-info">{{ $plan->name }}</span>
                    @endforeach
                </td>
                <td>
                    @foreach($promoCode->packages as $id => $plan)
                        <span class="badge badge-info">{{ number_format($plan->price,0,'.',' ') }}</span>
                    @endforeach
                </td>
                <td>
                    <button class="btn {{ $promoCode->status=='0' ? 'btn-outline-info' : 'btn-outline-success' }}">
                        {{ $promoCode->status=='0' ? 'Generated' : 'By Admin' }}
                    </button>
                </td>
                <td>
                    @can('premium_users_edit')
                        <a class="btn btn-xs btn-info" href="{{ route('admin.promoCodes.edit', $promoCode->id) }}">
                            {{ trans('global.edit') }}
                        </a>
                    @endcan
                    @can('premium_users_delete')
                        <form action="{{ route('admin.promoCodes.destroy', $promoCode->id) }}" method="POST"
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
