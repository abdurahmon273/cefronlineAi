@extends('layouts.admin')

@section('styles')
    <style>
        /* Style for all <a> elements */
        a {
            color: black; /* Black text color */
            text-decoration: none; /* No underline */
            font-weight: normal; /* Regular font weight */
        }

        a:hover{
            color: black; /* Black text color */
            text-decoration: none; /* No underline */
            font-weight: normal; /* Regular font weight */
        }


    </style>
@endsection
@section('content')

    <div class="card">
        <div class="card-header">
            <p style="font-size: 30px">{{ trans('cruds.writing.title') }} {{ trans('cruds.writing.fields.exercises') }}</p>
        </div>

        <div class="card-body">
            <div class="table-responsive">

                    @livewire('admin.writing.writing-index-livewire')
{{--                    <livewire:admin.writing.writing-index-livewire ></livewire:admin.writing.writing-index-livewire>--}}

            </div>
        </div>
    </div>

@endsection
