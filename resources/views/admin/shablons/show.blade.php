@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.shablon.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.shablons.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.shablon.fields.id') }}
                        </th>
                        <td>
                            {{ $shablon->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.shablon.fields.name') }}
                        </th>
                        <td>
                            {{ $shablon->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.shablon.fields.description') }}
                        </th>
                        <td>
                            {{ $shablon->description }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.shablons.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection