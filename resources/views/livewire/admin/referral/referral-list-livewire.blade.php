<div class="table-responsive">
    <div class="d-flex justify-content-between align-items-start mb-2">
        <button class="btn btn-secondary border " wire:click="toggleFilters" >Filter</button>

        <div style="min-width: 180px;">
            <select wire:model.live="selectedReferralMonth" class="form-control">
                @foreach($availableReferralMonths as $month)
                    <option value="{{ $month['value'] }}">{{ $month['label'] }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row mb-3  p-2 border {{ $showFilters ? '' : 'd-none' }} " id="filter_div">
        <div class="col-6">
            <input type="text" class="form-control mb-2 " placeholder="Search Users..." wire:model.live="search" />
        </div>
        <div class="col-6">
            <button wire:click="export" wire:loading.attr="disabled" class="btn btn-outline-success w-75">
                <span wire:loading.remove>Excel Export</span>
                <span wire:loading>Yuklanmoqda...</span>
            </button>

        </div>

        <div class="col-6" >
            <input type="text" id="start_date" class="form-control" placeholder="Start Date" wire:model.lazy="start_date">
        </div>
        <div class="col-6" >
            <input type="text" id="end_date" class="form-control" placeholder="End Date" wire:model.lazy="end_date">
        </div>




    </div>
    <div class="">

        <h5>Active Summa: {{ number_format(($filteredActiveSum),'0','.',' ') }} so`m</h5>
        <h5>Paid Summa: {{ number_format(($filteredPaidSum),'0','.',' ') }} so`m</h5>
    </div>


    <table class=" table table-bordered table-striped table-hover datatable datatable-City">
        <thead>
        <tr>
            <th width="10"></th>
            <th style="width: 4%"> {{ trans('cruds.city.fields.id') }} </th>
            <th> {{ trans('cruds.premium_users.fields.name') }} </th>
            <th> {{ trans('cruds.premium_users.fields.phone_number') }} </th>
            <th> {{ trans('cruds.referral.fields.active_price') }} </th>
            <th> {{ trans('cruds.referral.fields.paid_price') }} </th>
            <th> {{ trans('cruds.referral.fields.total_price') }} </th>
            <th style="width: 14%">&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $key=>$user)
            <tr data-entry-id="{{ $user?->id }}">
                <td>{{$key+1}}</td>
                <td> {{ $user?->id ?? '' }} </td>
                <td> {{ optional($user)->name ?? '—' }} {{ optional($user)->surname ?? '' }} </td>
                <td> {{ optional($user)->mobil_number ?? '' }} </td>
                <td class="text-right">
                   <span class="text-success ">
                       {{number_format($user->active_price,0,'.',' ')}}
                   </span>
                </td>
                <td class="text-right">
                   <span class="text-danger ">
                       {{number_format($user->paid_price,0,'.',' ')}}
                   </span>
                </td>
                <td class="text-right">
                   <span class=" ">
                       {{number_format($user->total_price,0,'.',' ')}}
                   </span>
                </td>

                <td class="d-flex justify-content-center">

                    <form action="{{ route('admin.pay.referral.index',['id'=>$user->id])}}" method="get">
                        @csrf
                        <input type="hidden" name="startDate" value="{{$start_date}}" id="">
                        <input type="hidden" name="endDate" value="{{$end_date}}" id="">
                        <button type="submit" class="btn btn-xs btn-info">
                            billing ->
                        </button>
                    </form>


                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <!-- Livewire pagination qo‘llash -->
    <div class="">
        {{ $users->links() }}
    </div>



</div>
