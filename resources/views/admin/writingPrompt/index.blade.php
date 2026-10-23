@extends('layouts.admin')
@section('content')
@can('information_create')
    <div style="margin-bottom: 10px;" class="row">

    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.writingPrompt.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Region">
                <thead>
                    <tr>

                        <th>
                            {{ trans('cruds.writingPrompt.fields.id') }}
                        </th>
                        <th  style="width: 15% !important;" >
                            {{ trans('cruds.writingPrompt.fields.type') }}
                        </th>
                        <th>
                            {{ trans('cruds.writingPrompt.fields.text_1') }}
                        </th>
                        <th>
                            {{ trans('cruds.writingPrompt.fields.text_2') }}
                        </th>
                        <th style="width: 10% !important;">
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($writingPrompts as $key => $writingPrompt)
                        <tr data-entry-id="{{ $writingPrompt->id }}">

                            <td style="width: 5%">
                                {{ $writingPrompt->id ?? '' }}
                            </td>
                            <td style="width: 15%">
                                {{ $types[$writingPrompt->type] ?? '' }}
                            </td>
                            <td style="width: 35%">
                                {{ \Illuminate\Support\Str::limit($writingPrompt->input_prompt_text ?? '', 80, '...') }}
                            </td>
                            <td style="width: 35%">
                                {{ \Illuminate\Support\Str::limit($writingPrompt->output_prompt_text ?? '', 80, '...') }}
                            </td>


                            <td style="width: 10%">

                                @can('information_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.writingPrompt.edit', $writingPrompt->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
            <table class=" table table-bordered table-striped table-hover datatable datatable-Region">
                <thead>
                    <tr>

                        <th>
                            {{ trans('cruds.writingPrompt.fields.id') }}
                        </th>
                        <th  style="width: 15% !important;" >
                            {{ trans('cruds.writingPrompt.fields.type') }}
                        </th>
                        <th>
                            {{ trans('cruds.writingPrompt.fields.text_1') }}
                        </th>
                        <th>
                            {{ trans('cruds.writingPrompt.fields.text_2') }}
                        </th>
                        <th style="width: 10% !important;">
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($speakingPrompts as $key => $speakingPrompt)
                        <tr data-entry-id="{{ $speakingPrompt->id }}">

                            <td style="width: 5%">
                                {{ $speakingPrompt->id ?? '' }}
                            </td>
                            <td style="width: 15%">
                                {{ $speakingTypes[$speakingPrompt->type] ?? '' }}
                            </td>
                            <td style="width: 35%">
                                {{ \Illuminate\Support\Str::limit($speakingPrompt->input_prompt_text ?? '', 80, '...') }}
                            </td>
                            <td style="width: 35%">
                                {{ \Illuminate\Support\Str::limit($speakingPrompt->output_prompt_text ?? '', 80, '...') }}
                            </td>


                            <td style="width: 10%">

                                @can('information_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.speakingPrompt.edit', $speakingPrompt->id) }}">
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
