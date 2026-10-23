@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.savedLesson.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.saved-lessons.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.savedLesson.fields.id') }}
                        </th>
                        <td>
                            {{ $savedLesson->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.savedLesson.fields.user') }}
                        </th>
                        <td>
                            {{ $savedLesson->user->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.savedLesson.fields.saved') }}
                        </th>
                        <td>
                            {{ App\Models\SavedLesson::SAVED_SELECT[$savedLesson->saved] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.savedLesson.fields.liked') }}
                        </th>
                        <td>
                            {{ App\Models\SavedLesson::LIKED_SELECT[$savedLesson->liked] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.savedLesson.fields.video') }}
                        </th>
                        <td>
                            {{ $savedLesson->video->link ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.saved-lessons.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection