@extends('../../master_layout/admin_master')

@section('title')
<title>Buy & Sell App</title>
@endsection

@section('content_header')
 <a href="{{url('admin-category-banner-add')}} "><i class="icon-home"></i>New Category-Banner</a>
@endsection

@section('custom_js')
<script src="{{asset('public/assets/Myfile/banner_js.js')}}"></script>
@endsection

@section('content')

 <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-info-sign"></i> </span>
            <h5>Category Banner Add</h5>
        </div>   
        <div class="widget-content nopadding">          
        @if(Session::has('message'))
        <div class="alert alert-info">
            <a class="close" data-dismiss="alert">×</a>
            <strong>!</strong> {{Session::get('message')}}
            {{Session::forget('message')}}
        </div>
        @endif
            
            <form class="form-horizontal" id="admin_banner_add" name="admin_category_banner_add" action="admin-category-banner-add-post" method="post" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="control-group">
                    <label class="control-label">Category:</label>
                    <div class="controls " >  
                       
                        <select name="category_id" id="category_id">
                            <option value="">Select Category</option>
                            @if(!empty($category))
                            @foreach($category as $cat)
                             <option value="{{$cat->id}}">{{$cat->category_name}}</option>
                            @endforeach
                            @endif
                        </select>   
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Banner Name</label>
                    <div class="controls">
                        <input type="text" name="category_banner_name" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Banner Image </label>
                    <div class="controls">
                        <input type="file" name="category_banner_img"/>
                    </div>                
                </div>

                <div class="control-group">
                    
                    <div class="controls">
                        <input type="submit" class="btn btn-default" name="category_banner_submit" id="banner_submit" value="submit"/>
                    </div>
                </div>
                
            </form>   
              




        </div>
    </div>


@endsection
