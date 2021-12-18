@extends('../../master_layout/admin_master')

@section('title')
<title>Buy & Sell App</title>
@endsection

@section('content_header')
 <a href="{{url('admin-subcategory-list')}} "><i class="icon-home"></i>Sub-Category</a>
@endsection

@section('custom_js')
<script src="{{asset('public/assets/Myfile/item_js.js')}}"></script>
<script src="{{asset('public/assets/Myfile/category_js.js')}}"></script>
@endsection

@section('content')

<div class="span12">
    

    <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-th"></i> </span>
            <h5 class="pull-left">Sub-Category Search</h5>
        </div>
        <div class="widget-content nopadding">

            <form class="form-horizontal" method="GET" action="" >
                <div class="control-group">
                    <label class="control-label"><b>Search by category:</b></label>
                    <div class="controls " >  
                       
                        <select name="category_id" id="category_id">
                            <option value="0">Select Category</option>
                            @if(!empty($category))
                            @foreach($category as $cat)
                             <option value="{{$cat->id}}"  @if(!empty($search['category_id']) && $search['category_id'] == $cat->id ) {{"selected"}} @endif>{{$cat->category_name}}</option>
                            @endforeach
                            @endif
                        </select>   
                    </div>
                </div>

                
                 <div class="control-group">
                    <label class="control-label"><b>Search by  Sub-Category :</b></label>
                    <div class="controls " >  
                       <input type="text" name="search_key" value="@if(!empty($search['search_key'])){{$search['search_key']}}@endif">
                    </div>
                </div>
                <div class="form-actions">
                    <input type="submit" value="Filter" class="btn btn-success">
                    <a href="{{URL::to('admin-subcategory-list')}}" class="btn btn-default">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-th"></i> </span>
            <h5 class="pull-left">Sub-Category list</h5>
             <a href="{{ URL::to('admin-subcategory-add')}}" class="btn btn-mini btn-primary pull-right button_class">Add New</a>
        </div>
        @if(!empty($subcategory_list))
        <div class="widget-content nopadding">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>SL.No</th>
<!--                        <th>Subcategory image</th>-->
                        <th>Category name</th>
                        <th>Sub-Category Name</th>
<!--                        <th>Subcategory Description</th>-->
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($subcategory_list as $subcat) 
                    <tr>
                        <td class="center">{{ $subcat->id}} </td>
<!--                        <td class="center"> <img src="{{asset('public/assets/images/'.$subcat->subcategory_image)}}" alt="Smiley face" height="42" width="42"></td>-->
                        <td class="center"> {{ ucwords($subcat->category_name)}}</td>
                        <td class="center"> {{ ucwords($subcat->subcategory_name)}}</td>
<!--                        <td class="center"> {{ $subcat->subcategory_description}}</td>-->
                        
                        <td class="center"> @if ($subcat->status==1) {{"Active"}} @else {{"Block"}}  @endif               
                        </td> 
                       
                        

                        <td class=" ">
                            <input type="hidden" id="sub_cat_details_{{$subcat->id}}" value="{{json_encode($subcat)}}">
                            
                             @if ($subcat->status==1)  
                             <a class="btn btn-mini mergin_one" onclick="subcat_chenge_status('{{base64_encode(env('APP_KEY').'||'.$subcat->id)}}')" >
                                <i class="icon-ban-circle"></i> {{"block"}} 
                            </a>
                             @else 
                             <a class="btn btn-mini mergin_one" onclick="subcat_chenge_status('{{base64_encode(env('APP_KEY').'||'.$subcat->id)}}')" >
                                <i class="icon-ban-circle"></i> {{"Active"}}      
                                </a>
                             @endif    
                            

                            &nbsp;&nbsp;
                            <a href="{{ URL::to('admin-subcategory-edit-'.base64_encode($cat->id.'||'.env('APP_KEY')))}}"  class="btn btn-mini mergin_one" >
                                <i class="icon-edit"></i> Edit</a>
                            &nbsp;&nbsp;
                            <a onclick="Delete_subcategory('{{base64_encode(env('APP_KEY').'||'.$subcat->id)}}')"  class="btn btn-mini" style="margin:1px">
                                <i class="icon-trash"></i> Delete</a>
                            &nbsp;&nbsp;
                           

                        </td>

                    </tr>
                    @endforeach

                </tbody>
            </table>

        </div>
        <div class="pagination alternate pull-right">
            {!! $subcategory_list->appends(['category_id'=>Request::get('category_id'),'search_key'=>Request::get('search_key')])->links() !!}
        </div>
        @endif
    </div>
</div>


@endsection
