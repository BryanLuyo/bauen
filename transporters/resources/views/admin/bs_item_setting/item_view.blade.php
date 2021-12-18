@extends('../../master_layout/admin_master')

@section('title')
<title>Buy & Sell App</title>
@endsection

@section('content_header')
 <a href="{{url('admin-user-post')}} "><i class="icon-home"></i> Ads</a>
@endsection

@section('custom_js')
<script src="{{asset('public/assets/Myfile/item_js.js')}}"></script>
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
            <h5 class="pull-left">Ad Search</h5>
        </div>
        @if(!empty($search))
       <?php //print_r($search) ;?> 
        @endif
        <div class="widget-content nopadding">

            <form class="form-horizontal" method="GET" action="" >
                <div class="control-group">
                    <label class="control-label"><b>Search by category: </b></label>
                    <div class="controls " >  
                       
                        <select name="category_id" id="category_id">
                            <option value="">Select Category </option>
                            @if(!empty($category))
                            @foreach($category as $cat)
                             <option value="{{$cat->id}}" @if(!empty($search['category_id']) && $search['category_id'] == $cat->id ) {{"selected"}} @endif>{{$cat->category_name}}</option>
                            @endforeach
                            @endif
                        </select>   
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label"><b>Search by sub-category:</b></label>
                    <div class="controls " >  
                        <select name="subcategory_id" id="subcategory_id">
                            <option value="" > Select Sub-Category </option> 
                                @if(!empty($select_subcat))
                                    @foreach($select_subcat as $subcat)
                                    <option value="{{$subcat->id}}" @if(!empty($search['subcategory_id']) && $search['subcategory_id'] == $subcat->id ) {{"selected"}} @endif>{{$subcat->subcategory_name}}</option>
                                    @endforeach
                                @endif     
                        </select> 
                        <input type="hidden" id="set_sub_cat" name="set_sub_cat">
                    </div>
                </div> 
                <div class="control-group">
                    <label class="control-label"><b>Search by User Name:</b></label>
                    <div class="controls " >  
                       <input type="text" name="post_user_name" value="@if(!empty($search['post_user_name'])){{$search['post_user_name']}}@endif">
                    </div>
                </div>
                 <div class="control-group">
                    <label class="control-label"><b>Search by Ads:</b></label>
                    <div class="controls " >  
                       <input type="text" name="search_key" value="@if(!empty($search['search_key'])){{$search['search_key']}}@endif">
                    </div>
                </div>
                <div class="form-actions">
                    <input type="submit" id="submit_item_search" value="Filter" class="btn btn-success">
                    <a href="{{URL::to('admin-user-post')}}" class="btn btn-default">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-th"></i> </span>
            <h5 class="pull-left">Ad list</h5>

        </div>
        @if(!empty($product_list))
        <div class="widget-content nopadding">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>SL.No</th>

                        <th>Post Name</th>
                        <th>Sold by.</th>
                        <th>Price</th>
                        <th>Category</th>
                        <th>Sub-Category</th>
                        
                        <th>Image</th>
                        <th>Tags</th>
                        <th>Status</th>
                        <th>Action</th>

                    </tr>
                </thead>
                <tbody>

                    @foreach ($product_list as $product) 
                    <tr>
                        <td class="center">{{ $product->id}} </td>

                        <td class="center"> {{ ucwords($product->item_name)}}</td>
                        <td class="center"> {{ ucwords($product->user_name)}}</td>
                        <td class="center"> {{ $product->price}}</td>
                         <td class="center"> {{ $product->category_name}}</td>
                        <td class="center"> {{ $product->subcategory_name}}</td>
                        
						 @if(!empty($product->item_image_name))
						<td class="center"><img src="{{asset('public/assets/images/'.$product->item_image_name)}}" height="100" width="150"></td>
                        @else
						<td class="center"><img src="{{asset('public/assets/images/02_default_image.png')}}" height="100" width="150" alt=""></td>	
						@endif
                        <td class="center"> {{ $product->item_tags}}</td>
                        <td class="center"> 
                            <span class="product_status" id="product_status_{{$product->id}}">
                                @if ($product->status==1) {{"Active"}} @else {{"Blocked"}}  @endif
                            </span>
                        </td> 
                        <td class=" ">
                            <input type="hidden" id="item_details_{{$product->id}}" value="{{json_encode($product)}}">
                            <input type="hidden" id="item_details_status_{{$product->id}}" value="{{$product->status}}">
							<input type="hidden" id="item_details_featured_status_{{$product->id}}" value="{{$product->is_premium_item}}">
                             
                             <a class="btn btn-mini mergin_one" onclick="chenge_status('{{base64_encode(env('APP_KEY').'||'.$product->id)}}')" >
                                 <i class="icon-ban-circle"></i>
                                 <span class="product_status_change" id="product_status_change_{{$product->id}}"> 
                                     @if ($product->status==1){{"Block"}} @else {{"Active"}} @endif   
                                 </span> 
                            </a>
                             
                            
                            &nbsp;&nbsp;
                            <a href="{{ URL::to('admin-edit-user-post-'.base64_encode($product->id.'||'.env('APP_KEY')))}}"  class="btn btn-mini mergin_one" >
                                <i class="icon-edit"></i> Edit</a>
                           
                            &nbsp;&nbsp;
                            <a href="{{ URL::to('admin-user-post-details-'.base64_encode($product->id.'||'.env('APP_KEY')))}}" class="btn btn-mini mergin_one" >
                                <i class="icon-dashboard"></i> Details</a>
                            &nbsp;&nbsp;
											
                            <a class="btn btn-mini mergin_one" onclick="chenge_featured_status('{{base64_encode(env('APP_KEY').'||'.$product->id)}}')" >
                                 <i class="icon-check"></i>
                                 <span id="product_featured_change_{{$product->id}}"> 
                                     @if($product->is_premium_item == 1){{"Featured"}} @else {{"Non-Featured"}} @endif   
                                 </span> 
                            </a>
                            

                        </td>

                    </tr>
                    @endforeach

                </tbody>
            </table>

        </div>
        <div class="pagination alternate pull-right">
            {!! $product_list->appends(['subcategory_id'=>Request::get('subcategory_id'),'set_sub_cat'=>Request::get('set_sub_cat'),'category_id'=>Request::get('category_id'),'post_user_name'=>Request::get('post_user_name'),'search_key'=>Request::get('search_key')])->setPath('')->links() !!}
        </div>
		@else
			 <div class="widget-content nopadding">
			<h3>  No results found </h3>
			</div>
        @endif
    </div>
</div>

@endsection
