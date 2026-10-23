@extends('layouts.admin')
@section('content')
    @can('information_create')
        <div style="margin-bottom: 10px;" class="row">

        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.globalSetting.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-Region">
                    <thead>
                    <tr>

                        <th  style="width: 30% !important;" >
                            {{ trans('cruds.globalSetting.fields.type') }}
                        </th>
                        <th>
                            {{ trans('cruds.globalSetting.fields.value') }}
                        </th>

                        <th style="width: 10% !important;">
                            &nbsp;
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr >
                        <td>
                            Open Ai Chat Url
                        </td>
                        <td >
                            {{ $globalSetting->openai_chat_url ?? '' }}
                        </td>

                        <td style="width: 10%">

                        </td>

                    </tr>
                    <tr >
                        <td>
                            Openai Model
                        </td>
                        <td >
                            {{ $globalSetting->openai_model ?? '' }}
                        </td>

                        <td style="width: 10%">

                        </td>

                    </tr>
                    <tr >
                        <td>
                            Open Max Tokens
                        </td>
                        <td >
                            {{ $globalSetting->openai_max_tokens ?? '' }}
                        </td>

                        <td style="width: 10%">

                        </td>

                    </tr>
                    <tr >
                        <td>
                            OpenAi Key
                        </td>
                        <td >
                            {{ $globalSetting->openai_key ?? '' }}
                        </td>

                        <td style="width: 10%">

                        </td>

                    </tr>
                    <tr >
                        <td>
                            Cloud Vision Ai  Key
                        </td>
                        <td >
                            {{ $globalSetting->cloud_vision_key ?? '' }}
                        </td>

                        <td style="width: 10%">

                        </td>

                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td>
                            @can('information_edit')
                                <a class="btn btn-xs btn-info" href="{{ route('admin.gptSettings.edit') }}">
                                    {{ trans('global.edit') }}
                                </a>
                            @endcan
                        </td>
                    </tr>

                    </tbody>
                </table>
                <table class=" table table-bordered table-striped table-hover datatable datatable-Region">
                    <thead>
                    <tr>

                        <th  style="width: 30% !important;" >
                            {{ trans('cruds.globalSetting.fields.type') }}
                        </th>
                        <th>
                            {{ trans('cruds.globalSetting.fields.value') }}
                        </th>

                        <th style="width: 10% !important;">
                            &nbsp;
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr >
                        <td>
                            GPT price Per Input Token
                        </td>
                        <td  style="text-align: end">
                            {{ $globalSetting->gpt_price_per_input_token ?? '' }}
                        </td>

                        <td style="width: 10%">

                        </td>

                    </tr>
                    <tr >
                        <td>
                            GPT price Per Output Token
                        </td>
                        <td  style="text-align: end">
                            {{ $globalSetting->gpt_price_per_output_token ?? '' }}
                        </td>

                        <td style="width: 10%">

                        </td>

                    </tr>

                    <tr >
                        <td>
                            Min Ai Credit Price
                        </td>
                        <td  style="text-align: end">
                            {{ $globalSetting->min_ai_credit_price ?? '' }}
                        </td>

                        <td style="width: 10%">

                        </td>

                    </tr>
                    <tr >
                        <td>
                            USD Current Currency
                        </td>
                        <td  style="text-align: end">
                            {{ $globalSetting->usd_current_currency ?? '' }}
                        </td>

                        <td style="width: 10%">

                        </td>

                    </tr>
                    <tr >
                        <td>
                            Chat Gpt Tax
                        </td>
                        <td  style="text-align: end">
                            {{ $globalSetting->chat_gpt_tax ?? '' }}%
                        </td>

                        <td style="width: 10%">

                        </td>

                    </tr>
                    <tr >
                        <td>
                            Profit Tax
                        </td>
                        <td  style="text-align: end">
                            {{ $globalSetting->profit_tax ?? '' }}%
                        </td>

                        <td style="width: 10%">

                        </td>

                    </tr>
                    <tr >
                        <td>
                            One Writing Price
                        </td>
                        <td  style="text-align: end">
                            {{ $globalSetting->one_writing_price ?? '' }}
                        </td>

                        <td style="width: 10%">

                        </td>

                    </tr>
                    <tr >
                        <td>
                            One Speaking Price
                        </td>
                        <td  style="text-align: end">
                            {{ $globalSetting->one_speaking_price ?? '' }}
                        </td>

                        <td style="width: 10%">

                        </td>

                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td>
                            @can('information_edit')
                                <a class="btn btn-xs btn-info" href="{{ route('admin.financeSettings.edit') }}">
                                    {{ trans('global.edit') }}
                                </a>
                            @endcan
                        </td>
                    </tr>

                    </tbody>
                </table>
                <table class=" table table-bordered table-striped table-hover datatable datatable-Region">
                    <thead>
                    <tr>

                        <th  style="width: 30% !important;" >
                            {{ trans('cruds.globalSetting.fields.type') }}
                        </th>
                        <th>
                            {{ trans('cruds.globalSetting.fields.value') }}
                        </th>

                        <th style="width: 10% !important;">
                            &nbsp;
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr >
                        <td>
                            Referral Profit Percentage
                        </td>
                        <td  style="text-align: end">
                            {{ $globalSetting->referral_profit_percentage ?? '' }}%
                        </td>

                        <td style="width: 10%">

                        </td>

                    </tr>
                    <tr >
                        <td>
                            Admin Contact Link
                        </td>
                        <td  style="text-align: end">
                            {{ $globalSetting->admin_contact ?? '' }}
                        </td>

                        <td style="width: 10%">

                        </td>

                    </tr>
                    <tr >
                        <td>
                            Referral Start Date
                        </td>
                        <td  style="text-align: end">
                            {{ $globalSetting->start_referral_date ?? '' }}
                        </td>

                        <td style="width: 10%">

                        </td>

                    </tr>

                    <tr>
                        <td></td>
                        <td></td>
                        <td>
                            @can('information_edit')
                                <a class="btn btn-xs btn-info" href="{{ route('admin.extraSettings.edit') }}">
                                    {{ trans('global.edit') }}
                                </a>
                            @endcan
                        </td>
                    </tr>

                    </tbody>
                </table>


                @livewire('admin.global-setting.telegram-bot-setting-livewire')

            </div>
        </div>
    </div>



@endsection
@section('scripts')
    @parent
    <script>

    </script>
@endsection
