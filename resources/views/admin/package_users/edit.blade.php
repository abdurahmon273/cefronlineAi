@extends('layouts.admin')
@section('styles')
    <style>
        .width35{
            width: 35% !important;
        }
        .DisplayNone{
            display: none !important;
        }
    </style>
@endsection
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.package_users.title_singular') }}
        </div>

        @livewire('admin.package.package-users-edit-livewire',['packageToUsers'=>$packageToUsers])


    </div>



@endsection
