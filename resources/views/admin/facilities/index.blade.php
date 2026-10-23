@extends('layouts.admin')
@section('content')
@can('city_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.facilities.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.facility.fields.title') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.facility.title') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-City">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th style="width: 10%">
                            {{ trans('cruds.city.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.facility.fields.title') }}
                        </th>

                        <th style="width: 15%">
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($facilities as $key => $facility)
                        <tr data-entry-id="{{ $facility->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $facility->id ?? '' }}
                            </td>
                            <td>
                                {{ $facility->title ?? '' }}
                            </td>

                            <td>

                                @can('city_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.facilities.edit', $facility->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('city_delete')
                                    <form action="{{ route('admin.facilities.destroy', $facility->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                    </form>
                                @endcan

                            </td>

                        </tr>
                    @endforeach
                </tbody>
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
