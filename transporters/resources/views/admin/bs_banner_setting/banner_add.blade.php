@extends('../../master_layout/admin_master')

@section('title')
<title>Buy & Sell App</title>
@endsection

@section('content_header')
 <a href="{{url('admin-banner-add')}} "><i class="icon-home"></i>New Banner</a>
@endsection

@section('custom_js')
<script src="{{asset('public/assets/Myfile/banner_js.js')}}"></script>
@endsection

@section('content')

 <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-info-sign"></i> </span>
            <h5>Banner Add</h5>
        </div>   
        <div class="widget-content nopadding">          
        @if(Session::has('message'))
        <div class="alert alert-info">
            <a class="close" data-dismiss="alert">×</a>
            <strong>!</strong> {{Session::get('message')}}
            {{Session::forget('message')}}
        </div>
        @endif
            
            <form class="form-horizontal" id="admin_banner_add" name="admin_banner_add" action="admin-banner-add-post" method="post" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
           
                <div class="control-group">
                    <label class="control-label">Banner Name</label>
                    <div class="controls">
                        <input type="text" name="banner_name" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Banner Image </label>
                    <div class="controls">
                        <input type="file" name="banner_img"/>
                    </div>                
                </div>

                <div class="control-group">
                    
                    <div class="controls">
                        <input type="submit" class="btn btn-default" name="banner_submit" id="banner_submit" value="submit"/>
                    </div>
                </div>
                
            </form>   
              




        </div>
    </div>


@endsection
