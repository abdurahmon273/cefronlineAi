@extends('layouts.admin')
@section('content')
@can('information_create')
    <div style="margin-bottom: 10px;" class="row">

    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.prepareText.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Region">
                <thead>
                    <tr>

                        <th>
                            {{ trans('cruds.prepareText.fields.id') }}
                        </th>
                        <th  style="width: 15% !important;" >
                            {{ trans('cruds.prepareText.fields.type') }}
                        </th>
                        <th>
                            {{ trans('cruds.prepareText.fields.text') }}
                        </th>
                        <th style="width: 10% !important;">
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prepareTexts as $key => $prepareText)
                        <tr data-entry-id="{{ $prepareText->id }}">

                            <td>
                                {{ $prepareText->id ?? '' }}
                            </td>
                            <td>
                                {{ $types[$prepareText->type] ?? '' }}
                            </td>
                            <td>
                                {{ $prepareText->text ?? '' }}
                            </td>

                            <td>

                                @can('information_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.prepareText.edit', $prepareText->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
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
