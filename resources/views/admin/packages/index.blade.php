@extends('layouts.admin')
@section('content')
@can('city_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.packages.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.package.title') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.package.title') }} {{ trans('global.list') }}
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
                        {{ trans('cruds.package.fields.price') }}
                    </th>
                    <th>
                        {{ trans('cruds.package.fields.credit_price') }}
                    </th>
                    <th>
                        {{ trans('cruds.package.fields.writing_count') }}
                    </th>
                    <th>
                        {{ trans('cruds.package.fields.speaking_count') }}
                    </th>
                    <th style="width:15%">
                        &nbsp;
                    </th>
                </tr>
                </thead>

                <livewire:admin.package.index-livewire ></livewire:admin.package.index-livewire>

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
