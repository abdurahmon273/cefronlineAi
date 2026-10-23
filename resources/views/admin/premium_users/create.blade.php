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
            {{ trans('global.create') }} {{ trans('cruds.premium_users.title_singular') }}
        </div>

        @livewire('premium.premium-users.premium-users-create-livewire')


    </div>



@endsection
