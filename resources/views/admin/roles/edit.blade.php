<?php
const VIEW_NAME    = 'admin.roles.edit';
?>

@include('admin.roles._routes')

@extends ('app')

@section('headings')
    <h1>Edit Role: {{ $model->name }}</h1>
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render(VIEW_NAME) !!}
@endsection


@section('content')

    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-primary">
                {!! Form::model($model,['method' =>'PUT',
                        'route'  => [UPDATE_ROUTE, $model->id],
                        'class'=>'form-horizontal']) !!}
                    @include('partials.crud.edit_buttons')
                    @include('admin.roles._form',['readonly' => false])
                    @include('partials.crud.edit_buttons')
                {!! Form::close() !!}
                <div class="panel-footer">
                    @include('partials.errors')
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
@endsection

