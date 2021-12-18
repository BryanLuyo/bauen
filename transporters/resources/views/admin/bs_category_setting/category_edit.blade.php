@extends('../../master_layout/admin_master')

@section('title')
<title>Buy & Sell App</title>
@endsection

@section('content_header')
 <a href="{{url('admin-category-list')}} "><i class="icon-home"></i> Category</a>
@endsection

@section('custom_js')
<script src="{{asset('public/assets/Myfile/category_js.js')}}"></script>
@endsection

@section('content')

 <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-info-sign"></i> </span>
            <h5>Category Edit</h5>
        </div>   
        <div class="widget-content nopadding">          
        @if(Session::has('message'))
        <div class="alert alert-info">
            <a class="close" data-dismiss="alert">×</a>
            <strong>!</strong> {{Session::get('message')}}
            {{Session::forget('message')}}
        </div>
        @endif            
            <form class="form-horizontal" id="admin_category_edit" name="admin_category_edit" action="admin-category-edit-post" method="post" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            
            <input type="hidden" id="category_id" name="category_id" value="{{$category->id}}">
            
                <div class="control-group">
                    <label class="control-label">Category Name</label>
                    <div class="controls">
                        <input type="text" name="category_name" value="{{$category->category_name}}" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Category Logo </label>
                    <div class="controls">
                        <input type="file" name="category_image" value="{{$category->category_image}}"/>
                        <img src="{{asset('public/assets/images/'.$category->category_image)}}" alt="Smiley face" height="42" width="42">
                    </div>                
                </div>
                <div class="control-group">
                    
                    <div class="controls">
                        <input type="submit" class="btn btn-default" name="category_submit" id="category_submit" value="submit"/>
                    </div>
                </div>                
            </form> 
        </div>
    </div>
@endsection
