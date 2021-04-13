@extends('backend.layouts.app')

@section('title', app_name() . ' | Activity Log')

@section('content')
    <div class="card" style="margin-top: 10px; font-size: 80%;">
        <div class="card-body">
            {!! $dataTable->table(['class'=>'table table-striped table-bordered', 'style'=>'width: 100%'],true) !!}
        </div><!--card-body-->
    </div><!--card-->
@endsection

@push('after-scripts')
    {!! $dataTable->scripts() !!}
@endpush

