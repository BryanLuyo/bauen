@extends('../../master_layout/admin_master')

@section('title')
<title>Buy & Sell App</title>
@endsection

@section('content_header')
 <a href="{{url('admin-slider')}} "><i class="icon-home"></i>slider</a>
@endsection

@section('custom_js')
<script src="{{asset('public/assets/Myfile/banner_js.js')}}"></script>
@endsection

@section('content')

 <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-info-sign"></i> </span>
            <h5>Slider Edit</h5>
        </div>   
        <div class="widget-content nopadding">          
        @if(Session::has('message'))
        <div class="alert alert-info">
            <a class="close" data-dismiss="alert">×</a>
            <strong>!</strong> {{Session::get('message')}}
            {{Session::forget('message')}}
        </div>
        @endif
            
            <form class="form-horizontal" name="admin_slider_edit" action="admin-slider-edit-post" method="post" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" id="slider_id" name="slider_id" value="{{$slider_details->id}}">
                <div class="control-group">
                    <label class="control-label">Slider Name</label>
                    <div class="controls">
                        <input type="text" name="title"  value="{{$slider_details->title}}"/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">slider Image </label>
                    <div class="controls">
                        <input type="file" name="image"/>
                         <img src="{{asset('public/assets/images/'.$slider_details->image)}}" alt="Smiley face" height="42" width="42">
                    </div>                
                </div>

                <div class="control-group">
                    
                    <div class="controls">
                        <input type="submit" class="btn btn-default" name="slider_submit" id="slider_submit" value="submit"/>
                    </div>
                </div>
                
            </form> 
        </div>
    </div>


@endsection
