
<div>
<form method="POST" action="{{ route('admin.globalSettings.loginBot.update') }}">
@csrf
<table class="table table-bordered table-striped table-hover">
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
            Login Bot Token
        </td>
        <td  style="text-align: end">
      <textarea
          class="form-control w-100"
          name="login_bot_token"
          rows="1"
          style="height: 36px; min-height: 46px; resize: none; overflow: hidden;"
      >{{ old('login_bot_token', $login_bot_token) }}</textarea>
        </td>

        <td style="width: 10%">
            @can('information_edit')
            <button type="submit" class="btn btn-xs btn-success">save</button>
            @endcan
        </td>

    </tr>

    <tr>
        <td></td>
        <td></td>
        <td>
            @can('information_edit')
                <a class="btn btn-xs btn-info" href="{{ route('admin.extraSettings.edit') }}">
                    reload
                </a>
            @endcan
        </td>
    </tr>

    </tbody>
</table>
</form>
</div>
