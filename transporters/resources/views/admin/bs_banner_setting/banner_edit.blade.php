@extends('../../master_layout/admin_master')

@section('title')
<title>Buy & Sell App</title>
@endsection

@section('content_header')
 <a href="{{url('admin-banner')}} "><i class="icon-home"></i>Banner</a>
@endsection

@section('custom_js')
<script src="{{asset('public/assets/Myfile/banner_js.js')}}"></script>
@endsection

@section('content')

 <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-info-sign"></i> </span>
            <h5>Banner Edit</h5>
        </div>   
        <div class="widget-content nopadding">          
        @if(Session::has('message'))
        <div class="alert alert-info">
            <a class="close" data-dismiss="alert">×</a>
            <strong>!</strong> {{Session::get('message')}}
            {{Session::forget('message')}}
        </div>
        @endif
            
            <form class="form-horizontal" id="admin_banner_add" name="admin_banner_edit" action="admin-banner-edit-post" method="post" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" id="banner_id" name="banner_id" value="{{$banner_details->id}}">
                <div class="control-group">
                    <label class="control-label">Banner Name</label>
                    <div class="controls">
                        <input type="text" name="banner_name"  value="{{$banner_details->banner_name}}"/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Banner Image </label>
                    <div class="controls">
                        <input type="file" name="banner_img"/>
                         <img src="{{asset('public/assets/images/'.$banner_details->banner_img)}}" alt="Smiley face" height="42" width="42">
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
