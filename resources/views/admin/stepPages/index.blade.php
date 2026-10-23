@extends('layouts.admin')

@section('content')
    @can('stepPage_create')
        <div class="mb-2 row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.stepPages.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.stepPage.title_singular') }}
                </a>
            </div>
        </div>
    @endcan

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <span>{{ trans('cruds.stepPage.title_singular') }} {{ trans('global.list') }}</span>
            <small class="text-muted">
                 {{ trans('cruds.stepPage.title')}}
            </small>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-0">
                    <thead>
                    <tr>
                        <th></th>
                        <th>ID</th>
                        <th>{{ trans('cruds.stepPage.fields.title') }}</th>
                        <th>{{ trans('cruds.stepPage.fields.description') }}</th>
                        <th>{{ trans('cruds.stepPage.fields.image') }}</th>
                        <th style="width: 15% !important;">{{ trans('global.actions') }}</th>
                    </tr>
                    </thead>
                    <livewire:admin.stepPage.index-livewire :stepPages="$stepPages"></livewire:admin.stepPage.index-livewire>

                </table>
            </div>
        </div>

    </div>
@endsection
