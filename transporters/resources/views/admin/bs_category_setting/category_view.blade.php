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
            <h5 class="pull-left">Category Search</h5>
        </div>
        <div class="widget-content nopadding">

            <form class="form-horizontal" method="GET" action="" >
                
                 <div class="control-group">
                    <label class="control-label"><b>Search by name:</b></label>
                    <div class="controls " >  
                       <input type="text" name="search_key" value="@if(!empty($search['search_key'])){{$search['search_key']}}@endif">
                    </div>
                </div>
                <div class="form-actions">
                    <input type="submit" value="Filter" class="btn btn-success">
                    <a href="{{URL::to('admin-category-list')}}" class="btn btn-default">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-th"></i> </span>
            <h5 class="pull-left">Category list</h5>
            <a href="{{ URL::to('admin-category-add')}}" class="btn btn-mini btn-primary pull-right button_class">Add New</a>
        </div>
        @if(!empty($category_list))
        <div class="widget-content nopadding">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>SL.No</th>
                        <th>Category Image</th>
                        <th>Category Name</th>
<!--                        <th>Category description</th>-->
                        <th>Status</th>
                        <th>Action</th>

                    </tr>
                </thead>
                <tbody>

                    @foreach ($category_list as $cat) 
                    <tr>
                        <td class="center">{{ $cat->id}} </td>
                        <td class="center"> <img src="{{asset('public/assets/images/'.$cat->category_image)}}" alt="Smiley face" height="42" width="42"></td>
                        <td class="center"> {{ ucwords($cat->category_name)}}</td>
<!--                        <td class="center"> {{ ucwords($cat->category_description)}}</td>-->
                        
                        <td class="center"> @if ($cat->status==1) {{"Active"}} @else {{"Block"}}  @endif 
                        
                            
                        </td> 
                       
                        

                        <td>
                            <input type="hidden" id="category_details_{{$cat->id}}" value="{{json_encode($cat)}}">
                            
                             @if ($cat->status==1)  
                             <a class="btn btn-mini mergin_one" onclick="chenge_cat_status('{{base64_encode(env('APP_KEY').'||'.$cat->id)}}')" >
                                <i class="icon-ban-circle"></i> {{"block"}} 
                            </a>
                             @else 
                             <a class="btn btn-mini mergin_one" onclick="chenge_cat_status('{{base64_encode(env('APP_KEY').'||'.$cat->id)}}')" >
                                <i class="icon-ban-circle"></i> {{"Active"}}      
                                </a>
                             @endif
                            &nbsp;&nbsp;
                            <a href="{{ URL::to('admin-category-edit-'.base64_encode($cat->id.'||'.env('APP_KEY')))}}"  class="btn btn-mini mergin_one" >
                                <i class="icon-edit"></i> Edit</a>
                            &nbsp;&nbsp;
                            <a href="{{ URL::to('admin-category-delete-'.base64_encode($cat->id.'||'.env('APP_KEY')))}}" class="btn btn-mini mergin_one">
                                <i class="icon-trash"></i> Delete</a>
                            &nbsp;&nbsp;
                           

                        </td>

                    </tr>
                    @endforeach

                </tbody>
            </table>

        </div>
        <div class="pagination alternate pull-right">
            {!! $category_list->appends(['search_key'=>Request::get('search_key')])->links() !!}
        </div>
        @endif
    </div>
</div>


@endsection
