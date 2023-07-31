@extends('layouts.core.backend', [
    'menu' => 'Category',
])

@section('title', trans('messages.template.category'))

@section('page_header')

    <div class="page-title">
        <ul class="breadcrumb breadcrumb-caret position-right">
            <li class="breadcrumb-item"><a href="{{ action('Admin\HomeController@index') }}">{{ trans('messages.home') }}</a>
            </li>
            <li class="breadcrumb-item"><a
                    href="{{ action('Admin\TemplateController@index') }}">{{ trans('messages.templates') }}</a></li>
            <li class="breadcrumb-item"><a href="#">{{ trans('messages.template.category') }}</a></li>
        </ul>
    </div>

@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1><span class="text-semibold">{{ trans('messages.new') }} {{ trans('messages.template.category') }}</span></h1>
            <form action="{{ action('Admin\CategoryController@store') }}" method="POST" class="template-form form-validate-jquery row">
                {{ csrf_field() }}
                <div class="col-md-6">
                    <div class="sub_section">
                        @include('helpers.form_control', [
                            'type' => 'text',
                            'class' => '',
                            'name' => 'name',
                            'value' => '',
                            'label' => trans('messages.template.category.create'),
                            'help_class' => 'template',
                            'rules' => ['name' => 'required'],
                        ])
                    </div>
                </div>
                <div class="col-md-6 mt-2 pt-3">
                    <button class="btn btn-secondary start-design"><i class="icon-check"></i>{{ trans('messages.create') }}</button>
                </div>
            </form>
        </div>
        <div class="col-md-12">
            <h1><span class="text-semibold">{{ trans('messages.new') }} {{ trans('messages.category.title') }}</span></h1>    
            <table class="table">
               <thead>
                    <tr>
                        <th>#</th>
                        <th>Created at</th>
                        <th>Name</th>
                        <th>Action</th>
                    </tr>
               </thead>
               <tbody>
                    @foreach ($category as $key => $item)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$item->created_at->format('m-d-Y')}}</td>
                            <td>
                                <form action="{{ action('Admin\CategoryController@update', $item->id) }}" method="post" class="d-flex">
                                    @csrf
                                    @method('PATCH')
                                    <input type="text" name="name" class="form-control w-75" value="{{$item->name}}" id="">
                                    <button class="btn btn-secondary start-design ml-3"><i class="icon-check"></i>{{ trans('messages.update') }}</button>
                                </form>
                            </td>
                            <td>
                                <a link-method="delete" link-confirm="{{ trans('messages.category.delete.warning') }}"
                                    href="{{ action('Admin\CategoryController@destroy', $item->id) }}"
                                    class="btn btn-danger me-2"
                                >
                                    {{ trans('messages.delete') }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
               </tbody>
            </table>
        </div>
    </div>
@endsection
