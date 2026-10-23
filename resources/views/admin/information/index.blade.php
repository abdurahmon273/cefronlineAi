@extends('layouts.admin')
@section('content')
@can('information_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.information.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.information.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.information.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Region">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.information.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.information.fields.link') }}
                        </th>
                        <th>
                            {{ trans('cruds.information.fields.type') }}
                        </th>
                        <th style="width: 15% !important;">
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($informations as $key => $information)
                        <tr data-entry-id="{{ $information->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $information->id ?? '' }}
                            </td>
                            <td>
                                {{ $information->information_link ?? '' }}
                            </td>
                            <td>
                                {{ $types[$information->type] ?? '' }}
                            </td>
                            <td>

                                @can('information_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.information.edit', $information->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('information_delete')
                                    <form action="{{ route('admin.information.destroy', $information->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
