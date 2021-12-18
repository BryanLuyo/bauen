@extends('../../master_layout/admin_master')

@section('title')
<title>Buy & Sell App</title>
@endsection

@section('content_header')
 <a href="{{url('admin-banner')}} "><i class="icon-home"></i>Banner</a>
@endsection

@section('custom_js')
<script src="{{asset('public/assets/Myfile/banner_js.js')}}"></script>
<!--<script src="{{asset('public/assets/Myfile/category_js.js')}}"></script>-->
@endsection

@section('content')

<div class="span12">
     @if(Session::has('message'))
        <div class="alert alert-info">
            <a class="close" data-dismiss="alert">×</a>
            <strong>!</strong> {{Session::get('message')}}
            {{Session::forget('message')}}
        </div>
        @endif

    <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-th"></i> </span>
            <h5 class="pull-left">Banner list</h5>
             <a href="{{ URL::to('admin-banner-add')}}" class="btn btn-mini btn-primary pull-right button_class">Add New</a>
        </div>
        @if(!empty($banner_list))
        <div class="widget-content nopadding">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>SL.No</th>
                        <th>Banner name</th>
                        <th>Image</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($banner_list as $banner) 
                    <tr>
                        <td class="center">{{ $banner->id}} </td>

                        <td class="center"> {{ ucwords($banner->banner_name)}}</td>
              <td class="center"> <img src="{{asset('public/assets/images/'.$banner->banner_img)}}" alt="Smiley face" height="42" width="42"></td>
                        
                        <td class="center"> @if ($banner->status==1) {{"Active"}} @else {{"Block"}}  @endif               
                        </td> 
                       
                        

                        <td class=" ">
                            <input type="hidden" id="banner_details_{{$banner->id}}" value="{{json_encode($banner)}}">
                            
                             @if ($banner->status==1)  
                             <a class="btn btn-mini mergin_one" onclick="banner_chenge_status('{{base64_encode(env('APP_KEY').'||'.$banner->id)}}')" >
                                <i class="icon-ban-circle"></i> {{"block"}} 
                            </a>
                             @else 
                             <a class="btn btn-mini mergin_one" onclick="banner_chenge_status('{{base64_encode(env('APP_KEY').'||'.$banner->id)}}')" >
                                <i class="icon-ban-circle"></i> {{"Active"}}      
                                </a>
                             @endif    
                            

                            &nbsp;&nbsp;
                            <a href="{{ url('admin-banner-edit-'.base64_encode($banner->id.'||'.env("APP_KEY")))}}"  class="btn btn-mini mergin_one" >
                                <i class="icon-edit"></i> Edit</a>
                            &nbsp;&nbsp;
                            <a onclick="return confirm('Are you sure you want to delete this banner?');" href="{{ url('admin-banner-delete-'.base64_encode($banner->id.'||'.env("APP_KEY")))}}" class="btn btn-mini" style="margin:1px">
                                <i class="icon-trash"></i> Delete</a>
                            &nbsp;&nbsp;
                           

                        </td>

                    </tr>
                    @endforeach

                </tbody>
            </table>

        </div>
        <div class="pagination alternate pull-right">
            {!! $banner_list->appends([Request::only(''),
                Request::only('')
            ])->links() !!}
        </div>
        @endif
    </div>
</div>
@endsection
