@extends('layouts.admin')
@section('content')
@can('city_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.plans.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.plan.title') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.plan.title') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-City">
                <thead>
                <tr>
                    <th width="10"></th>
                    <th style="width: 3%">
                        {{ trans('cruds.city.fields.id') }}
                    </th>
                    <th>
                        {{ trans('cruds.plan.fields.name') }}
                    </th>
                    <th>
                        {{ trans('cruds.plan.fields.price') }}
                    </th>
                    <th>
                        {{ trans('cruds.plan.fields.days') }}
                    </th>
                    <th>
                        {{ trans('cruds.plan.fields.ai_credit') }}
                    </th>
                    <th style="width:15%">
                        &nbsp;
                    </th>
                </tr>
                </thead>

                <livewire:admin.plan.index-livewire ></livewire:admin.plan.index-livewire>

            </table>
        </div>
    </div>
</div>



@endsection
@section('scripts')
@parent
<script>

</script>
@endsection
